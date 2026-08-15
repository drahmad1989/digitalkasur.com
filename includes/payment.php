<?php
/**
 * Payment Integration - DigitalKasur.com
 * JazzCash & EasyPaisa Integration
 */

require_once __DIR__ . '/../config.php';

class PaymentGateway {

    /**
     * Generate JazzCash Payment Request
     */
    public static function jazzCashRequest($amount, $order_id, $description = '') {
        $datetime = new DateTime();
        $timestamp = $datetime->format('YmdHis');

        $data = [
            'pp_Amount' => strval($amount * 100), // Amount in paisas
            'pp_BillReference' => $order_id,
            'pp_Description' => $description ?: 'DigitalKasur Service Payment',
            'pp_Language' => 'EN',
            'pp_MerchantID' => JAZZCASH_MERCHANT_ID,
            'pp_Password' => JAZZCASH_PASSWORD,
            'pp_ReturnURL' => JAZZCASH_RETURN_URL,
            'pp_SecureHash' => '',
            'pp_TxnCurrency' => 'PKR',
            'pp_TxnDateTime' => $timestamp,
            'pp_TxnExpiryDateTime' => $datetime->modify('+1 hour')->format('YmdHis'),
            'pp_TxnRefNo' => 'TXN' . $timestamp . rand(1000, 9999),
            'pp_Version' => '1.1',
            'pp_SubMerchantID' => '',
            'pp_BankID' => '',
            'pp_ProductID' => '',
            'pp_TxnType' => '',
            'pp_MPRA' => '',
            'pp_MobileNumber' => '',
            'pp_CNIC' => '',
        ];

        // Calculate Secure Hash
        ksort($data);
        $hash_string = JAZZCASH_INTEGRITY_SALT . '&';
        foreach ($data as $key => $value) {
            if ($value !== '' && $key !== 'pp_SecureHash') {
                $hash_string .= $value . '&';
            }
        }
        $hash_string = rtrim($hash_string, '&');
        $data['pp_SecureHash'] = hash_hmac('sha256', $hash_string, JAZZCASH_INTEGRITY_SALT);

        return $data;
    }

    /**
     * Verify JazzCash Payment Response
     */
    public static function verifyJazzCashResponse($response) {
        $secure_hash = $response['pp_SecureHash'] ?? '';
        unset($response['pp_SecureHash']);

        ksort($response);
        $hash_string = JAZZCASH_INTEGRITY_SALT . '&';
        foreach ($response as $key => $value) {
            if ($value !== '') {
                $hash_string .= $value . '&';
            }
        }
        $hash_string = rtrim($hash_string, '&');
        $calculated_hash = hash_hmac('sha256', $hash_string, JAZZCASH_INTEGRITY_SALT);

        return hash_equals($calculated_hash, $secure_hash);
    }

    /**
     * Generate EasyPaisa Payment Request
     */
    public static function easypaisaRequest($amount, $order_id, $email = '', $phone = '') {
        $datetime = new DateTime();
        $timestamp = $datetime->format('YmdHis');

        $data = [
            'storeId' => EASYPAISA_MERCHANT_ID,
            'orderId' => $order_id,
            'transactionAmount' => $amount,
            'transactionType' => 'MA',
            'emailAddress' => $email,
            'cellNumber' => $phone,
            'accountType' => 'MA',
        ];

        return $data;
    }

    /**
     * Save Payment Record
     */
    public static function savePayment($data) {
        $payment_data = [
            'user_id' => $data['user_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => 'PKR',
            'gateway' => $data['gateway'], // jazzcash or easypaisa
            'reference' => $data['reference'],
            'order_id' => $data['order_id'] ?? null,
            'description' => $data['description'] ?? '',
            'status' => $data['status'] ?? 'pending',
            'customer_name' => $data['customer_name'] ?? '',
            'customer_email' => $data['customer_email'] ?? '',
            'customer_phone' => $data['customer_phone'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return DB::insert('payments', $payment_data);
    }

    /**
     * Update Payment Status
     */
    public static function updatePaymentStatus($reference, $status, $gateway_response = '') {
        return DB::update('payments', [
            'status' => $status,
            'gateway_response' => $gateway_response,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'reference = ?', [$reference]);
    }

    /**
     * Get Payment by Reference
     */
    public static function getPayment($reference) {
        return DB::selectOne("SELECT * FROM payments WHERE reference = ?", [$reference]);
    }

    /**
     * Get Payment by Order ID
     */
    public static function getPaymentByOrder($order_id) {
        return DB::selectOne("SELECT * FROM payments WHERE order_id = ?", [$order_id]);
    }

    /**
     * Generate WhatsApp Payment Link
     * For manual payment tracking via WhatsApp
     */
    public static function generateWhatsAppPaymentLink($amount, $description) {
        $message = urlencode("Assalam o Alaikum! Main payment karna chahta hoon:\n\nService: {$description}\nAmount: PKR " . number_format($amount) . "\n\nPlease send payment details.");
        return "https://wa.me/" . ADMIN_WHATSAPP . "?text=" . $message;
    }
}

/**
 * Get JazzCash Form HTML
 */
function getJazzCashForm($amount, $order_id, $description = '') {
    $data = PaymentGateway::jazzCashRequest($amount, $order_id, $description);
    $action_url = PAYMENT_MODE === 'sandbox'
        ? 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/Payment/DoTransaction'
        : 'https://jazzcash.com.pk/ApplicationAPI/API/Payment/DoTransaction';

    $html = '<form method="POST" action="' . $action_url . '" id="jazzcash-form">';
    foreach ($data as $key => $value) {
        $html .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
    }
    $html .= '</form>';
    return $html;
}
?>
