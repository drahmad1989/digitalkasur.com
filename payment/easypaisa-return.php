<?php
/**
 * EasyPaisa Payment Return/Callback Handler - DigitalKasur.com
 *
 * Handles the payment response returned from EasyPaisa gateway.
 * Updates payment status in database and redirects the user
 * to the appropriate page.
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
// LOG THE RESPONSE FOR DEBUGGING
// ============================================================

$log_entry = date('Y-m-d H:i:s') . " | EasyPaisa Response GET: " . json_encode($_GET) . " POST: " . json_encode($_POST) . "\n";
@file_put_contents(__DIR__ . '/../logs/easypaisa_response.log', $log_entry, FILE_APPEND | LOCK_EX);

// ============================================================
// GET RESPONSE DATA (EasyPaisa can return via GET or POST)
// ============================================================

$response_data = !empty($_POST) ? $_POST : $_GET;

$order_id         = $response_data['orderId'] ?? '';
$store_id         = $response_data['storeId'] ?? '';
$transaction_id   = $response_data['transactionId'] ?? '';
$transaction_ref  = $response_data['transactionRef'] ?? '';
$amount           = isset($response_data['transactionAmount']) ? (float)$response_data['transactionAmount'] : 0;
$status_code      = $response_data['status'] ?? $response_data['responseCode'] ?? '';
$status_message   = $response_data['statusMessage'] ?? $response_data['responseMessage'] ?? '';
$payment_method   = $response_data['paymentMethod'] ?? '';
$email_address    = $response_data['emailAddress'] ?? '';
$cell_number      = $response_data['cellNumber'] ?? '';
$account_type     = $response_data['accountType'] ?? '';
$auth_code        = $response_data['authCode'] ?? '';
$signature        = $response_data['signature'] ?? '';

// ============================================================
// VALIDATE ORDER EXISTS
// ============================================================

$payment = null;
if ($order_id) {
    $payment = PaymentGateway::getPaymentByOrder($order_id);
}

if (!$payment && $transaction_ref) {
    $payment = PaymentGateway::getPayment($transaction_ref);
}

if (!$payment && $transaction_id) {
    $payment = PaymentGateway::getPayment($transaction_id);
}

// ============================================================
// DETERMINE PAYMENT STATUS
// ============================================================

// EasyPaisa status codes:
// '0000' or '00' = SUCCESS
// '0001' = PENDING
// Others = FAILED
$success_codes = ['0000', '00', '000', '0'];
$pending_codes = ['0001', '01', '1001'];

if (in_array((string)$status_code, $success_codes)) {
    $payment_status = 'completed';
} elseif (in_array((string)$status_code, $pending_codes)) {
    $payment_status = 'pending';
} else {
    $payment_status = 'failed';
}

// ============================================================
// VERIFY AMOUNT (Prevent amount manipulation)
// ============================================================

if ($payment && $payment_status === 'completed') {
    $expected_amount = (float)$payment['amount'];
    // Allow small floating-point difference
    if (abs($amount - $expected_amount) > 0.50) {
        error_log("EasyPaisa Amount Mismatch! Expected: {$expected_amount}, Received: {$amount}, OrderID: {$order_id}");
        $payment_status = 'failed';
        $status_message = 'Amount verification failed';
    }
}

// ============================================================
// UPDATE PAYMENT RECORD IN DATABASE
// ============================================================

$gateway_response = json_encode([
    'status_code' => $status_code,
    'status_message' => $status_message,
    'order_id' => $order_id,
    'store_id' => $store_id,
    'transaction_id' => $transaction_id,
    'transaction_ref' => $transaction_ref,
    'amount' => $amount,
    'payment_method' => $payment_method,
    'email_address' => $email_address,
    'cell_number' => $cell_number,
    'account_type' => $account_type,
    'auth_code' => $auth_code,
    'signature' => $signature,
    'raw_response' => $response_data
]);

if ($payment) {
    // Update existing payment record
    PaymentGateway::updatePaymentStatus($payment['reference'], $payment_status, $gateway_response);

    // If payment completed, update related records
    if ($payment_status === 'completed') {
        // Update event registration payment status if linked
        if ($payment['order_id']) {
            DB::update('event_registrations', [
                'payment_status' => 'paid',
                'payment_id' => $payment['id']
            ], 'event_id = ? AND email = ?', [
                $payment['order_id'],
                $payment['customer_email']
            ]);
        }
    }
} else {
    // Payment record not found — create one from response data
    $reference = $transaction_ref ?: $transaction_id ?: ('EP_' . time() . '_' . rand(1000, 9999));

    PaymentGateway::savePayment([
        'user_id' => $_SESSION['user_id'] ?? null,
        'amount' => $amount,
        'gateway' => 'easypaisa',
        'reference' => $reference,
        'order_id' => $order_id,
        'description' => 'EasyPaisa Payment - ' . $status_message,
        'status' => $payment_status,
        'customer_name' => $_SESSION['user_name'] ?? '',
        'customer_email' => $email_address,
        'customer_phone' => $cell_number,
    ]);

    error_log("EasyPaisa: Created new payment record for unknown order: {$order_id}");
}

// ============================================================
// SET FLASH MESSAGE AND REDIRECT
// ============================================================

$redirect_ref = $transaction_ref ?: $transaction_id ?: $order_id;

if ($payment_status === 'completed') {
    set_flash_message('success', 'Payment of PKR ' . number_format($amount) . ' completed successfully via EasyPaisa! Transaction ID: ' . $redirect_ref);
    redirect(SITE_URL . '/pages/payment-success.php?gateway=easypaisa&ref=' . urlencode($redirect_ref) . '&amount=' . urlencode($amount));
} elseif ($payment_status === 'pending') {
    set_flash_message('warning', 'Your EasyPaisa payment is being processed. You will be notified once confirmed. Transaction ID: ' . $redirect_ref);
    redirect(SITE_URL . '/pages/payment-pending.php?gateway=easypaisa&ref=' . urlencode($redirect_ref));
} else {
    set_flash_message('error', 'EasyPaisa payment failed: ' . $status_message . ' (Code: ' . $status_code . '). Please try again or contact support.');
    redirect(SITE_URL . '/pages/payment-failed.php?gateway=easypaisa&ref=' . urlencode($redirect_ref) . '&code=' . urlencode($status_code));
}

exit();
?>
