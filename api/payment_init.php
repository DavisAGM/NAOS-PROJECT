<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';
require '../includes/ctech_config.php';
require '../includes/ctech_pay_helper.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$order_id = intval($data['order_id'] ?? $_POST['order_id'] ?? 0);

if ($order_id <= 0) {
    // Check if it's a direct subscription request
    if ((isset($_GET['type']) && $_GET['type'] === 'subscription') || (isset($data['type']) && $data['type'] === 'subscription')) {
        $user_id = $_SESSION['user_id'];

        $amount = 5000; // Default
        $resSet = mysqli_query($conn, "SELECT setting_value FROM system_settings WHERE setting_key = 'subscription_amount'");
        if ($rowSet = mysqli_fetch_assoc($resSet)) {
            $amount = floatval($rowSet['setting_value']);
        }

        // Create order
        $stmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, type, quantity, price, status, payment_status) VALUES (?, 'subscription', 1, ?, 'pending', 'unpaid')");
        mysqli_stmt_bind_param($stmt, "id", $user_id, $amount);
        mysqli_stmt_execute($stmt);
        $order_id = mysqli_insert_id($conn);
    } else {
        echo json_encode(['error' => 'Invalid Order ID']);
        exit;
    }
}

// 1. Get the order details so we know how much to charge
$query = "SELECT o.*, u.username, u.phone_number 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.id = $order_id";
$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo json_encode(['error' => 'We could not find the details for this order.']);
    exit;
}

// Calculate total amount
$amount = $order['price']; // For subscriptions, price is the target. For produce, we'd need to fetch from listing if not in order price
if ($order['listing_id'] > 0 && $amount <= 0) {
    $qListing = mysqli_query($conn, "SELECT price FROM produce_listings WHERE id = " . $order['listing_id']);
    $listingData = mysqli_fetch_assoc($qListing);
    if ($listingData) {
        $amount = $order['quantity'] * $listingData['price'];
    }
}

$currency = 'MWK';
$tx_ref = 'NAOS-' . $order_id . '-' . time();

// 2. Create a pending payment record
$stmt = mysqli_prepare($conn, "INSERT INTO payments (transaction_id, order_id, amount, currency, status) VALUES (?, ?, ?, ?, 'pending')");
mysqli_stmt_bind_param($stmt, "sids", $tx_ref, $order_id, $amount, $currency);
if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['error' => 'We could not start the payment process. Please try again.']);
    exit;
}


$phone = $data['phone'] ?? $_POST['phone'] ?? $order['phone_number'] ?? '';

// Debug log for flow
$init_log = "[" . date('Y-m-d H:i:s') . "] Payment Init Flow:\n";
$init_log .= "Order ID: $order_id\n";
$init_log .= "Phone: $phone\n";
$init_log .= "Amount: $amount\n";
file_put_contents(__DIR__ . '/../ctech_debug.log', $init_log, FILE_APPEND);


if ($phone) {
    // 3. Initiate Mobile Payment (STK Push)
    $category_flag = 'NAOS'; // Required category flag
    $res = ctech_initiate_mobile_payment($phone, $amount, $category_flag);

    if ($res && (isset($res['status']) && $res['status'] === 'success')) {
        $order_reference = $res['data']['data']['transaction']['id'] ?? $res['order_reference'] ?? $res['data']['order_reference'] ?? '';

        
        if ($order_reference) {
            mysqli_query($conn, "UPDATE payments SET provider_ref = '$order_reference', payment_method = 'airtel' WHERE transaction_id = '$tx_ref'");
        }
        
        echo json_encode([
            'status' => 'success', 
            'success' => true,
            'message' => 'Payment initiated. Please check your phone for the STK push.',
            'order_reference' => $order_reference
        ]);
        exit;
    } else {
        echo json_encode(['error' => 'Mobile Payment initiation failed.', 'details' => $res]);
        exit;
    }
}

// 4. Fallback to generic Link/Order flow if no phone or as backup
$payload = [
    'token' => CTECHPAY_TOKEN,
    'registration' => CTECH_REGISTRATION,
    'amount' => $amount,
    'redirectUrl' => CTECH_RETURN_URL,
    'cancelUrl' => CTECH_CANCEL_URL,
    'merchantAttributes' => [
        'customer_name' => $order['username'],
        'order_id' => $order_id,
        'orderRef' => $tx_ref
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, CTECHPAY_API_URL . CTECHPAY_ORDER_ENDPOINT);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$err = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Log fallback flow
$fb_log = "[" . date('Y-m-d H:i:s') . "] Fallback Payment Link Flow:\n";
$fb_log .= "HTTP Code: $http_code\n";
$fb_log .= "Response: $response\n";
if ($err) $fb_log .= "Curl Error: $err\n";
$fb_log .= "-------------------\n";
file_put_contents(__DIR__ . '/../ctech_debug.log', $fb_log, FILE_APPEND);


if ($err) {
    echo json_encode(['error' => 'Payment Gateway Error: ' . $err]);
    exit;
}

$res = json_decode($response, true);

if ($res && (isset($res['success']) && ($res['success'] === true || $res['success'] === "true"))) {
    $payment_link = $res['payment_page_URL'] ?? $res['payment_link'] ?? $res['data']['payment_link'] ?? '';
    $order_reference = $res['order_reference'] ?? $res['data']['order_reference'] ?? '';

    if ($payment_link) {
        // Store order_reference if provided
        if ($order_reference) {
            mysqli_query($conn, "UPDATE payments SET provider_ref = '$order_reference' WHERE transaction_id = '$tx_ref'");
        }
        echo json_encode(['status' => 'success', 'checkout_url' => $payment_link]);
    } else {
        echo json_encode(['error' => 'We did not get a payment link from CTechPay.', 'debug' => $res]);
    }
} else {
    echo json_encode(['error' => 'CTechPay could not process the request.', 'details' => $res]);
}

?>