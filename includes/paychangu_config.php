<?php
// PayChangu Configuration
// Replace with actual keys from PayChangu Dashboard
define('PAYCHANGU_PUBLIC_KEY', 'pub-test-xxxxxxxxxxxxxxxxxxxx'); 
define('PAYCHANGU_SECRET_KEY', 'sec-test-xxxxxxxxxxxxxxxxxxxx');
define('PAYCHANGU_API_URL', 'https://api.paychangu.com/payment'); // Correct endpoint for Hosted Checkout
define('PAYCHANGU_VERIFY_URL', 'https://api.paychangu.com/verify-payment/'); // Correct endpoint for verification

// Local Development URLs
define('CALLBACK_URL', 'http://localhost/naos/api/payment_callback.php');
define('RETURN_URL', 'http://localhost/naos/buyer_dashboard.php');
?>
