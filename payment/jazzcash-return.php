<?php
/**
 * JazzCash Payment Return/Callback Handler - DigitalKasur.com
 *
 * Handles the payment response returned from JazzCash gateway.
 * Verifies the secure hash, updates payment status in database,
 * and redirects the user to the appropriate page.
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

$log_entry = date('Y-m-d H:i:s') . " | JazzCash Response: " . json_encode($_POST) . "\n";
@file_put_contents(__DIR__ . '/../logs/jazzcash_response.log', $log_entry, FILE_APPEND | LOCK_EX);

// ============================================================
// GET RESPONSE DATA
// ============================================================

$response_code    = $_POST['pp_ResponseCode'] ?? '';
$response_message = $_POST['pp_ResponseMessage'] ?? '';
$txn_ref          = $_POST['pp_TxnRefNo'] ?? '';
$txn_type         = $_POST['pp_TxnType'] ?? '';
$amount           = isset($_POST['pp_Amount']) ? (float)($_POST['pp_Amount'] / 100) : 0; // Convert from paisas
$bill_ref         = $_POST['pp_BillReference'] ?? '';
$secure_hash      = $_POST['pp_SecureHash'] ?? '';
$language         = $_POST['pp_Language'] ?? '';
$merchant_id      = $_POST['pp_MerchantID'] ?? '';
$txn_currency     = $_POST['pp_TxnCurrency'] ?? 'PKR';
$txn_datetime     = $_POST['pp_TxnDateTime'] ?? '';
$retreival_ref_no = $_POST['pp_RetreivalReferenceNo'] ?? '';
$bank_id          = $_POST['pp_BankID'] ?? '';
$sub_merchant_id  = $_POST['pp_SubMerchantID'] ?? '';
$mobile_number    = $_POST['pp_MobileNumber'] ?? '';
$cnic             = $_POST['pp_CNIC'] ?? '';

// ============================================================
// VERIFY SECURE HASH
// ============================================================

$hash_verified = PaymentGateway::verifyJazzCashResponse($_POST);

if (!$hash_verified) {
    // Hash verification failed — possible tampering
    error_log("JazzCash Hash Verification FAILED for TxnRef: {$txn_ref}");

    // Update payment status to failed if record exists
    if ($txn_ref) {
        PaymentGateway::updatePaymentStatus($txn_ref, 'failed', json_encode([
            'error' => 'Hash verification failed',
            'response_code' => $response_code,
            'response_message' => $response_message,
            'posted_data' => $_POST
        ]));
    }

    set_flash_message('error', 'Payment verification failed. Please contact support if you believe this is an error.');
    redirect(SITE_URL . '/pages/payment-failed.php?gateway=jazzcash&ref=' . urlencode($txn_ref));
    exit();
}

// ============================================================
// DETERMINE PAYMENT STATUS
// ============================================================

// JazzCash success response codes
$success_codes = ['000', '001', '100', '121'];

if (in_array($response_code, $success_codes)) {
    $payment_status = 'completed';
} elseif ($response_code === '124') {
    // Transaction pending / awaiting confirmation
    $payment_status = 'pending';
} else {
    $payment_status = 'failed';
}

// ============================================================
// UPDATE PAYMENT RECORD IN DATABASE
// ============================================================

$gateway_response = json_encode([
    'response_code' => $response_code,
    'response_message' => $response_message,
    'txn_ref' => $txn_ref,
    'txn_type' => $txn_type,
    'amount' => $amount,
    'bill_reference' => $bill_ref,
    'currency' => $txn_currency,
    'txn_datetime' => $txn_datetime,
    'retrieval_ref_no' => $retreival_ref_no,
    'bank_id' => $bank_id,
    'sub_merchant_id' => $sub_merchant_id,
    'mobile_number' => $mobile_number,
    'cnic' => substr($cnic, 0, 4) . '****' . substr($cnic, -4), // Mask CNIC for security
    'secure_hash_verified' => true,
    'raw_response' => $_POST
]);

// Find the payment record by reference
$payment = PaymentGateway::getPayment($txn_ref);

if ($payment) {
    // Update existing payment record
    PaymentGateway::updatePaymentStatus($txn_ref, $payment_status, $gateway_response);

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
    PaymentGateway::savePayment([
        'user_id' => $_SESSION['user_id'] ?? null,
        'amount' => $amount,
        'gateway' => 'jazzcash',
        'reference' => $txn_ref,
        'order_id' => $bill_ref,
        'description' => 'JazzCash Payment - ' . $response_message,
        'status' => $payment_status,
        'customer_name' => $_SESSION['user_name'] ?? '',
        'customer_email' => $_SESSION['user_email'] ?? '',
        'customer_phone' => $mobile_number,
    ]);

    error_log("JazzCash: Created new payment record for unknown reference: {$txn_ref}");
}

// ============================================================
// SET FLASH MESSAGE AND REDIRECT
// ============================================================

if ($payment_status === 'completed') {
    set_flash_message('success', 'Payment of PKR ' . number_format($amount) . ' completed successfully! Transaction Reference: ' . $txn_ref);
    redirect(SITE_URL . '/pages/payment-success.php?gateway=jazzcash&ref=' . urlencode($txn_ref) . '&amount=' . urlencode($amount));
} elseif ($payment_status === 'pending') {
    set_flash_message('warning', 'Your payment is being processed. You will be notified once confirmed. Transaction Reference: ' . $txn_ref);
    redirect(SITE_URL . '/pages/payment-pending.php?gateway=jazzcash&ref=' . urlencode($txn_ref));
} else {
    set_flash_message('error', 'Payment failed: ' . $response_message . ' (Code: ' . $response_code . '). Please try again or contact support.');
    redirect(SITE_URL . '/pages/payment-failed.php?gateway=jazzcash&ref=' . urlencode($txn_ref) . '&code=' . urlencode($response_code));
}

exit();
?>
