<?php
// API Credentials
define('CTECHPAY_TOKEN', 'a1ZXVnVXOEhhNGJIZW1IM3BZZWRKdGkwbk5UNmNhck1lQ0RQeVNuR3l0MjRobGRVcWh6ZGZZZ1ozdXd2S2ZrRA');
define('CTECH_REGISTRATION', 'NAOS');

// API Endpoints
define('CTECHPAY_API_URL', 'https://new-api.ctechpay.com/api/v1/');
define('CTECHPAY_ORDER_ENDPOINT', 'airtel/payment');
define('CTECHPAY_STATUS_ENDPOINT', 'airtel/payment/status');

// Redirect URLs
define('CTECH_RETURN_URL', 'http://localhost/naos/api/payment_callback.php');
define('CTECH_CANCEL_URL', 'http://localhost/naos/api/payment_callback.php?payment=cancelled');
?>
