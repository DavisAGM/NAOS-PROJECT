<?php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}



function send_sms($phone, $message)
{
    global $conn;
    $sms_provider = 'sms8'; // Can be 'sms8', 'twilio', or 'none'
    
    // SMS8 Credentials
    $sms8_api_key = 'b1b85f8d52e563b502519d81ff974eafd840c73f';
    $sms8_device_id = '10609';
    $sms8_sim_slot = '0';

    // Twilio Credentials
    if (!defined('TWILIO_ACCOUNT_SID')) define('TWILIO_ACCOUNT_SID', 'YOUR_ACCOUNT_SID');
    if (!defined('TWILIO_AUTH_TOKEN')) define('TWILIO_AUTH_TOKEN', 'YOUR_AUTH_TOKEN');
    if (!defined('TWILIO_PHONE_NUMBER')) define('TWILIO_PHONE_NUMBER', '+1234567890');

    if ($sms_provider === 'twilio') {
        $result = sendTwilioSMS($phone, $message);
        logSMS($conn, $phone, $message, $result['success'], 'Twilio', $result['error']);
        return $result['success'];
    } elseif ($sms_provider === 'sms8') {
        $result = sendSMS8($phone, $message, $sms8_api_key, $sms8_device_id, $sms8_sim_slot);
        logSMS($conn, $phone, $message, $result['success'], 'SMS8', $result['error']);
        return $result['success'];
    } else {
        logSMS($conn, $phone, $message, true, 'Development', null);
        return true;
    }
}

function sendVerificationSMS($phone, $code)
{
    $message = "Your Nyasa Agricultural Optimization System verification code is: $code. Do not share it with anyone.";
    return send_sms($phone, $message);
}


function logSMS($conn, $phone, $message, $success, $provider, $error = null)
{
    if ($conn) {
        $status = $success ? 'SUCCESS' : 'FAILED';
        $logMsg = "[$provider] [$status] SMS to $phone: $message";
        if ($error) {
            $logMsg .= " | Error: $error";
        }
        $stmt = mysqli_prepare($conn, "INSERT INTO logs (message, user_id) VALUES (?, NULL)");
        mysqli_stmt_bind_param($stmt, "s", $logMsg);
        mysqli_stmt_execute($stmt);
    }

    $logFile = __DIR__ . '/../sms_logs.txt';
    $timestamp = date('Y-m-d H:i:s');
    $status = $success ? 'SUCCESS' : 'FAILED';
    $logEntry = "[$timestamp] [$provider] [$status] To: $phone | Msg: $message";
    if ($error) {
        $logEntry .= " | Error: $error";
    }
    file_put_contents($logFile, $logEntry . PHP_EOL, FILE_APPEND);
}


function sendTwilioSMS($phone, $message)
{

    if (!class_exists('Twilio\Rest\Client')) {
        return [
            'success' => false,
            'error' => 'Twilio SDK not installed. Run: composer require twilio/sdk'
        ];
    }

    try {
        $formattedPhone = formatPhoneForTwilio($phone);

        $client = new Twilio\Rest\Client(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN);

        // Send SMS
        $twilioMessage = $client->messages->create(
            $formattedPhone, // To
            [
                'from' => TWILIO_PHONE_NUMBER,
                'body' => $message
            ]
        );

        return [
            'success' => true,
            'error' => null,
            'sid' => $twilioMessage->sid
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}


function formatPhoneForTwilio($phone)
{
    $phone = preg_replace('/[^0-9]/', '', $phone);

    if (substr($phone, 0, 1) === '0') {
        return '+265' . substr($phone, 1);
    }

    if (substr($phone, 0, 3) === '265') {
        return '+' . $phone;
    }

    if (substr($phone, 0, 4) === '+265') {
        return $phone;
    }

    return '+265' . $phone;
}


function sendSMS8($phone, $message, $api_key, $device_id, $sim_slot = '0')
{
    $devices = json_encode([$device_id . '|' . $sim_slot]);


    $params = [
        'key' => $api_key,
        'number' => $phone,
        'message' => $message,
        'devices' => $devices,
        'type' => 'sms',
        'prioritize' => '0'
    ];

    $url = 'https://app.sms8.io/services/send.php?' . http_build_query($params);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['success' => false, 'error' => 'cURL error: ' . $curl_error];
    }

    if ($http_code !== 200) {
        return ['success' => false, 'error' => 'HTTP error: ' . $http_code];
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['success' => false, 'error' => 'Invalid JSON: ' . json_last_error_msg()];
    }

    if (isset($data['success']) && $data['success'] === true) {
        return ['success' => true, 'error' => null];
    } elseif (isset($data['status']) && $data['status'] === 'success') {
        return ['success' => true, 'error' => null];
    } else {
        $error_msg = 'Unknown error';

        if (isset($data['error'])) {
            if (is_array($data['error']) && isset($data['error']['message'])) {
                $error_msg = $data['error']['message'];
            } elseif (is_string($data['error'])) {
                $error_msg = $data['error'];
            }
        } elseif (isset($data['message'])) {
            $error_msg = $data['message'];
        }

        $error_msg .= " | Raw Response: " . substr($response, 0, 200);
        return ['success' => false, 'error' => $error_msg];
    }
}
?>