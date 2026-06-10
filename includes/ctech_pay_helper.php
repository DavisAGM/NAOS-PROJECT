<?php
/**
 * CTech Pay Integration Helper
 * 
 * This file handles the communication with CTech Pay API for mobile payments.
 */

require_once __DIR__ . '/ctech_config.php';

/**
 * Initiate an Airtel Money payment request (STK Push)
 * 
 * @param string $phone The customer's phone number (e.g., 0999123456)
 * @param float|int $amount The amount to charge
 * @param string $category_flag The category of payment (e.g., LOAN_PAYMENT, SUBSCRIPTION)
 * @return array The API response decoded from JSON
 */
function ctech_initiate_mobile_payment($phone, $amount, $category_flag = 'SUBSCRIPTION') {
    $payload = [
        'token' => CTECHPAY_TOKEN,
        'amount' => (int)$amount,
        'phone' => $phone,
        'category_flag' => $category_flag,
    ];

    $url = CTECHPAY_API_URL . CTECHPAY_ORDER_ENDPOINT;
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $log_msg = "[" . date('Y-m-d H:i:s') . "] Mobile Payment Initiation:\n";
    $log_msg .= "URL: $url\n";
    $log_msg .= "Payload: " . json_encode($payload) . "\n";
    $log_msg .= "HTTP Code: $http_code\n";
    $log_msg .= "Response: $response\n";
    if ($error) $log_msg .= "Curl Error: $error\n";
    $log_msg .= "-------------------\n";
    file_put_contents(__DIR__ . '/../ctech_debug.log', $log_msg, FILE_APPEND);

    if ($error) {
        return [
            'success' => false,
            'error' => 'Connection Error: ' . $error
        ];
    }

    return json_decode($response, true);
}

/**
 * Check the status of a payment
 * 
 * @param string $order_reference The reference returned by the initiate call
 * @return array The status response
 */
function ctech_check_payment_status($order_reference) {
    if (!$order_reference) {
        return ['success' => false, 'error' => 'Missing order reference'];
    }

    $url = CTECHPAY_API_URL . 'airtel/status';
    
    $payload = [
        'token' => CTECHPAY_TOKEN,
        'trans_id' => $order_reference
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $log_msg = "[" . date('Y-m-d H:i:s') . "] Payment Status Check:\n";
    $log_msg .= "URL: $url\n";
    $log_msg .= "HTTP Code: $http_code\n";
    $log_msg .= "Response: $response\n";
    if ($error) $log_msg .= "Curl Error: $error\n";
    $log_msg .= "-------------------\n";
    file_put_contents(__DIR__ . '/../ctech_debug.log', $log_msg, FILE_APPEND);

    return json_decode($response, true);

}
