<?php
/**
 * Payment Processing - DigitalKasur.com
 *
 * Processes payment requests: validates amount/order details,
 * determines gateway (JazzCash/EasyPaisa), generates payment
 * form/data, saves initial payment record, and redirects to
 * the chosen payment gateway.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/payment.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// ONLY ACCEPT POST REQUESTS
// ============================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash_message('error', 'Invalid request method.');
    redirect(SITE_URL);
    exit();
}

// ============================================================
// VALIDATE CSRF TOKEN
// ============================================================

if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    set_flash_message('error', 'Invalid security token. Please try again.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

// ============================================================
// GET AND VALIDATE INPUT
// ============================================================

$amount       = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
$gateway      = isset($_POST['gateway']) ? clean_input($_POST['gateway']) : '';
$order_id     = isset($_POST['order_id']) ? clean_input($_POST['order_id']) : '';
$description  = isset($_POST['description']) ? clean_input($_POST['description']) : '';
$customer_name  = isset($_POST['customer_name']) ? clean_input($_POST['customer_name']) : '';
$customer_email = isset($_POST['customer_email']) ? clean_input($_POST['customer_email']) : '';
$customer_phone = isset($_POST['customer_phone']) ? clean_input($_POST['customer_phone']) : '';
$return_url     = isset($_POST['return_url']) ? clean_input($_POST['return_url']) : '';

// Type-specific identifiers
$event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
$job_id   = isset($_POST['job_id']) ? (int)$_POST['job_id'] : 0;

// ============================================================
// VALIDATE AMOUNT
// ============================================================

if ($amount <= 0) {
    set_flash_message('error', 'Invalid payment amount.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

if ($amount > 500000) {
    set_flash_message('error', 'Payment amount exceeds maximum limit of PKR 500,000.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

// ============================================================
// VALIDATE GATEWAY
// ============================================================

$allowed_gateways = ['jazzcash', 'easypaisa'];
if (!in_array($gateway, $allowed_gateways)) {
    set_flash_message('error', 'Invalid payment gateway selected. Please choose JazzCash or EasyPaisa.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

// ============================================================
// VALIDATE CUSTOMER DETAILS
// ============================================================

if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    set_flash_message('error', 'Please provide a valid email address.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

if (empty($customer_name)) {
    set_flash_message('error', 'Please provide your name.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

// ============================================================
// GENERATE ORDER ID IF NOT PROVIDED
// ============================================================

if (empty($order_id)) {
    $prefix = strtoupper(substr($gateway, 0, 2)); // JC or EP
    $order_id = $prefix . '_' . date('YmdHis') . '_' . rand(1000, 9999);
}

// ============================================================
// GENERATE UNIQUE PAYMENT REFERENCE
// ============================================================

$reference = 'TXN_' . strtoupper($gateway) . '_' . date('YmdHis') . '_' . rand(10000, 99999);

// ============================================================
// SAVE INITIAL PAYMENT RECORD
// ============================================================

$payment_id = PaymentGateway::savePayment([
    'user_id' => $_SESSION['user_id'] ?? null,
    'amount' => $amount,
    'gateway' => $gateway,
    'reference' => $reference,
    'order_id' => $order_id,
    'description' => $description ?: 'DigitalKasur Service Payment',
    'status' => 'pending',
    'customer_name' => $customer_name,
    'customer_email' => $customer_email,
    'customer_phone' => $customer_phone,
]);

if (!$payment_id) {
    error_log("Payment Process: Failed to save initial payment record. Ref: {$reference}");
    set_flash_message('error', 'Failed to initiate payment. Please try again.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

// ============================================================
// LOG PAYMENT ATTEMPT
// ============================================================

$log_entry = date('Y-m-d H:i:s') . " | Payment Initiated | ID: {$payment_id} | Ref: {$reference} | Gateway: {$gateway} | Amount: {$amount} | Order: {$order_id} | Email: {$customer_email}\n";
@file_put_contents(__DIR__ . '/../logs/payment_initiated.log', $log_entry, FILE_APPEND | LOCK_EX);

// ============================================================
// REDIRECT TO PAYMENT GATEWAY
// ============================================================

if ($gateway === 'jazzcash') {
    // ============================================================
    // JAZZCASH PAYMENT
    // ============================================================

    $jazzcash_data = PaymentGateway::jazzCashRequest($amount, $order_id, $description ?: 'DigitalKasur Service Payment');
    $action_url = PAYMENT_MODE === 'sandbox'
        ? 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/Payment/DoTransaction'
        : 'https://jazzcash.com.pk/ApplicationAPI/API/Payment/DoTransaction';

    // Generate and output auto-submit form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Redirecting to JazzCash - DigitalKasur</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Poppins', sans-serif; background: #f3f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
            .redirect-container { text-align: center; background: #fff; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 500px; width: 90%; }
            .spinner { width: 50px; height: 50px; border: 4px solid #e5e7eb; border-top-color: #1E40AF; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1.5rem; }
            @keyframes spin { to { transform: rotate(360deg); } }
            h2 { color: #1E40AF; margin-bottom: 0.5rem; font-size: 1.5rem; }
            p { color: #6b7280; font-size: 0.95rem; margin-bottom: 1rem; }
            .amount { font-size: 2rem; font-weight: 700; color: #1E40AF; margin: 1rem 0; }
            .gateway-badge { display: inline-flex; align-items: center; gap: 8px; background: #eff6ff; color: #1E40AF; padding: 8px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem; }
            .manual-link { color: #1E40AF; text-decoration: underline; cursor: pointer; font-size: 0.85rem; }
        </style>
    </head>
    <body>
        <div class="redirect-container">
            <div class="spinner"></div>
            <h2>Redirecting to JazzCash</h2>
            <p>Please wait while we redirect you to JazzCash for secure payment processing.</p>
            <div class="gateway-badge">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/JazzCash_Logo.svg/120px-JazzCash_Logo.svg.png" alt="JazzCash" height="20" onerror="this.style.display='none'">
                JazzCash Payment Gateway
            </div>
            <div class="amount">PKR <?php echo number_format($amount); ?></div>
            <p>If you are not redirected automatically within 5 seconds,</p>
            <a class="manual-link" onclick="document.getElementById('jazzcashForm').submit();">click here to proceed</a>

            <form method="POST" action="<?php echo htmlspecialchars($action_url); ?>" id="jazzcashForm">
                <?php foreach ($jazzcash_data as $key => $value): ?>
                    <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                <?php endforeach; ?>
            </form>
        </div>

        <script>
            // Auto-submit form after a short delay
            setTimeout(function() {
                document.getElementById('jazzcashForm').submit();
            }, 2000);
        </script>
    </body>
    </html>
    <?php

} elseif ($gateway === 'easypaisa') {
    // ============================================================
    // EASYPAISA PAYMENT
    // ============================================================

    $easypaisa_data = PaymentGateway::easypaisaRequest($amount, $order_id, $customer_email, $customer_phone);

    // EasyPaisa uses a POST form redirect approach
    $action_url = PAYMENT_MODE === 'sandbox'
        ? 'https://easypaystg.easypaisa.com.pk/easypay/Index.jsf'
        : 'https://easypaisa.com.pk/easypay/Index.jsf';

    // Store the payment reference in session for verification on return
    $_SESSION['easypaisa_pending_ref'] = $reference;
    $_SESSION['easypaisa_pending_order'] = $order_id;

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Redirecting to EasyPaisa - DigitalKasur</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Poppins', sans-serif; background: #f3f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
            .redirect-container { text-align: center; background: #fff; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 500px; width: 90%; }
            .spinner { width: 50px; height: 50px; border: 4px solid #e5e7eb; border-top-color: #36b37e; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1.5rem; }
            @keyframes spin { to { transform: rotate(360deg); } }
            h2 { color: #36b37e; margin-bottom: 0.5rem; font-size: 1.5rem; }
            p { color: #6b7280; font-size: 0.95rem; margin-bottom: 1rem; }
            .amount { font-size: 2rem; font-weight: 700; color: #36b37e; margin: 1rem 0; }
            .gateway-badge { display: inline-flex; align-items: center; gap: 8px; background: #ecfdf5; color: #36b37e; padding: 8px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem; }
            .manual-link { color: #36b37e; text-decoration: underline; cursor: pointer; font-size: 0.85rem; }
        </style>
    </head>
    <body>
        <div class="redirect-container">
            <div class="spinner"></div>
            <h2>Redirecting to EasyPaisa</h2>
            <p>Please wait while we redirect you to EasyPaisa for secure payment processing.</p>
            <div class="gateway-badge">
                <i class="fas fa-mobile-alt"></i>
                EasyPaisa Payment Gateway
            </div>
            <div class="amount">PKR <?php echo number_format($amount); ?></div>
            <p>If you are not redirected automatically within 5 seconds,</p>
            <a class="manual-link" onclick="document.getElementById('easypaisaForm').submit();">click here to proceed</a>

            <form method="POST" action="<?php echo htmlspecialchars($action_url); ?>" id="easypaisaForm">
                <?php foreach ($easypaisa_data as $key => $value): ?>
                    <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                <?php endforeach; ?>
            </form>
        </div>

        <script>
            setTimeout(function() {
                document.getElementById('easypaisaForm').submit();
            }, 2000);
        </script>
    </body>
    </html>
    <?php

} else {
    // Should not reach here due to earlier validation, but just in case
    set_flash_message('error', 'Invalid payment gateway.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

exit();
?>
