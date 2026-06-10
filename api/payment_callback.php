<?php
ob_start();
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';
require '../includes/ctech_config.php';
require '../includes/ctech_pay_helper.php';

$tx_ref = $_GET['ref'] ?? $_GET['orderRef'] ?? $_GET['tx_ref'] ?? ''; // Documentation says 'ref'

if (!$tx_ref) {
    if (isset($_GET['payment']) && $_GET['payment'] === 'cancelled') {
        header("Location: " . CTECH_CANCEL_URL);
        exit;
    }
    echo json_encode(['error' => 'No transaction reference provided']);
    exit;
}


// 1. Get Payment Record from local DB
$qLocal = mysqli_query($conn, "SELECT * FROM payments WHERE transaction_id = '$tx_ref' OR provider_ref = '$tx_ref'");
$localPayment = mysqli_fetch_assoc($qLocal);

if (!$localPayment) {
    // Try one more time with provider_ref specifically if $tx_ref looks like an ID
    $qLocal = mysqli_query($conn, "SELECT * FROM payments WHERE provider_ref = '$tx_ref'");
    $localPayment = mysqli_fetch_assoc($qLocal);
}

if (!$localPayment) {
    echo json_encode(['error' => 'Transaction not found']);
    exit;
}

$provider_ref = $localPayment['provider_ref'] ?? '';
$verification_status = 'failed';
$payment_method = $localPayment['payment_method'] ?? 'card';

if ($payment_method === 'airtel') {
    // Verify via Mobile Status API (GET)
    $res = ctech_check_payment_status($provider_ref ?: $tx_ref);
    if ($res) {
        $isSuccess = false;
        // Check various success indicators
        if (isset($res['success']) && ($res['success'] === true || $res['success'] === "true")) $isSuccess = true;
        if (isset($res['status']['success']) && ($res['status']['success'] === true || $res['status']['success'] === "true")) $isSuccess = true;
        if (isset($res['status']) && (is_string($res['status']) && strtolower($res['status']) === 'success')) $isSuccess = true;
        if (isset($res['transaction_status']) && $res['transaction_status'] === 'TS') $isSuccess = true;
        if (isset($res['message']) && strpos($res['message'], 'Successful') !== false) $isSuccess = true;

        // Airtel definitive failure transaction statuses:
        // TF = Transaction Failed, TE = Transaction Expired, TC = Transaction Cancelled
        $airtel_failed_statuses = ['TF', 'TE', 'TC'];
        if (isset($res['transaction_status']) && in_array($res['transaction_status'], $airtel_failed_statuses)) {
            $verification_status = 'failed';
            $failure_reason = $res['message'] ?? 'Payment was declined by Airtel.';
        } elseif ($isSuccess) {
            $verification_status = 'success';
            $provider_ref = $res['transaction_id'] ?? $res['data']['transaction_id'] ?? $res['data']['transaction']['id'] ?? $res['airtel_money_id'] ?? $provider_ref;
            $payment_method = 'airtel';
        } elseif (isset($res['transaction_status']) && $res['transaction_status'] === 'TIP') {
            $verification_status = 'pending';
        } elseif (isset($res['status']) && $res['status'] === 'PURCHASED') {
            $verification_status = 'success';
            $payment_method = 'airtel';
        }
    }
} else {
    // Verify via generic Student/Order Status API (POST)
    $payload = [
        'token' => CTECHPAY_TOKEN,
        'registration' => CTECH_REGISTRATION,
        'orderRef' => $tx_ref
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, CTECHPAY_API_URL . CTECHPAY_STATUS_ENDPOINT);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $res = json_decode($response, true);
    
    if ($res && isset($res['status']) && $res['status'] === 'PURCHASED') {
        $verification_status = 'success';
        $provider_ref = $res['transaction_id'] ?? $res['data']['transaction_id'] ?? $tx_ref;
        $payment_method = $res['payment_method'] ?? $res['data']['payment_method'] ?? 'card';
    }
}


// 2. Update Database
if ($verification_status === 'success') {
    // Update Payment
    $stmt = mysqli_prepare($conn, "UPDATE payments SET status = 'completed', provider_ref = ?, payment_method = ? WHERE transaction_id = ?");
    mysqli_stmt_bind_param($stmt, "sss", $provider_ref, $payment_method, $localPayment['transaction_id']);
    mysqli_stmt_execute($stmt);

    // Get Order ID from payments
    $q = mysqli_query($conn, "SELECT order_id FROM payments WHERE transaction_id = '" . $localPayment['transaction_id'] . "'");
    $row = mysqli_fetch_assoc($q);
    if ($row) {
        $order_id = $row['order_id'];
        // Update Order
        // Update Order
        $orderQ = mysqli_query($conn, "SELECT type FROM orders WHERE id = $order_id");
        $orderData = mysqli_fetch_assoc($orderQ);
        
        if ($orderData['type'] !== 'subscription') {
            mysqli_query($conn, "UPDATE orders SET payment_status = 'paid', escrow_status = 'paid', status = 'confirmed' WHERE id = $order_id");
        } else {
            mysqli_query($conn, "UPDATE orders SET payment_status = 'paid', status = 'completed' WHERE id = $order_id");
        }

        // Check if this is a subscription order
        $orderQ = mysqli_query($conn, "SELECT user_id, type FROM orders WHERE id = $order_id");
        if ($orderQ && mysqli_num_rows($orderQ) > 0) {
            $orderData = mysqli_fetch_assoc($orderQ);
            if ($orderData['type'] === 'subscription') {
                $userId = $orderData['user_id'];

                // Get current active subscription to calculate extension
                $currentSubQ = mysqli_query($conn, "SELECT expiry_date FROM subscriptions WHERE user_id = $userId AND status = 'active' ORDER BY expiry_date DESC LIMIT 1");
                $currentExpiry = null;
                if ($currentSubQ && mysqli_num_rows($currentSubQ) > 0) {
                    $subData = mysqli_fetch_assoc($currentSubQ);
                    $currentExpiry = strtotime($subData['expiry_date']);
                }

                // Calculate New Expiry: If exists and not yet reached, add to it. Otherwise, start from now.
                $baseTime = (strtotime('now') < $currentExpiry) ? $currentExpiry : strtotime('now');
                $newExpiryDate = date('Y-m-d H:i:s', strtotime('+365 days', $baseTime));

                // 1. Mark all previous active/expired subscriptions for this user as expired (cleanup)
                mysqli_query($conn, "UPDATE subscriptions SET status = 'expired' WHERE user_id = $userId AND (status = 'active' OR status = 'inactive')");

                // 2. Activate User main flag
                mysqli_query($conn, "UPDATE users SET subscription_status = 'active' WHERE id = $userId");

                // 3. Insert new active record with extended date
                $activeRole = getUserRole();
                $subStmt = mysqli_prepare($conn, "INSERT INTO subscriptions (user_id, status, plan_type, role_type, start_date, expiry_date) VALUES (?, 'active', 'monthly', ?, NOW(), ?)");
                mysqli_stmt_bind_param($subStmt, "iss", $userId, $activeRole, $newExpiryDate);
                mysqli_stmt_execute($subStmt);
            }
        }
    }

    // Get User ID for notification
    $targetUserId = 0;
    $qU = mysqli_query($conn, "SELECT user_id FROM orders WHERE id = (SELECT order_id FROM payments WHERE transaction_id = '" . $localPayment['transaction_id'] . "')");
    if ($rowU = mysqli_fetch_assoc($qU)) $targetUserId = $rowU['user_id'];

    if ($targetUserId > 0) {
        $msg = "Payment of " . number_format($localPayment['amount'], 2) . " MWK was successful. Your account has been updated.";
        $stmt_notif = mysqli_prepare($conn, "INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
        mysqli_stmt_bind_param($stmt_notif, "is", $targetUserId, $msg);
        mysqli_stmt_execute($stmt_notif);
    }

    // Redirect back to dashboard based on role
    $role = getUserRole();
    $target = ($role === 'farmer') ? '../farmer/dashboard.php' : '../buyer/dashboard.php';
    ob_end_clean();
    header("Location: " . $target . "?payment=success&order_id=" . ($order_id ?? ''));
    exit;

} else {
    if ($verification_status === 'pending') {
        $role = getUserRole();
        $target = ($role === 'farmer') ? '../farmer/dashboard.php' : '../buyer/dashboard.php';
        ob_end_clean();
        header("Location: " . $target . "?payment=pending");
        exit;
    }
    // Check if it's a definitive failure (Airtel TF/TE/TC already caught above)
    $isDefinitiveFailure = isset($failure_reason); // set above for Airtel TF/TE/TC
    if (!$isDefinitiveFailure && isset($res['status']) && in_array(strtoupper($res['status']), ['FAILED', 'CANCELLED', 'EXPIRED', 'DECLINED'])) {
        $isDefinitiveFailure = true;
        $failure_reason = $res['message'] ?? 'Payment was declined.';
    }

    if ($isDefinitiveFailure) {
        // Mark payment as failed in DB
        $stmt = mysqli_prepare($conn, "UPDATE payments SET status = 'failed' WHERE transaction_id = ?");
        mysqli_stmt_bind_param($stmt, "s", $localPayment['transaction_id']);
        mysqli_stmt_execute($stmt);

        $role = getUserRole();
        $target = ($role === 'farmer') ? '../farmer/dashboard.php' : '../buyer/dashboard.php';
        ob_end_clean();
        header("Location: " . $target . "?payment=failed&reason=" . urlencode($failure_reason ?? 'Payment failed.'));
        exit;
    } else {
        // It is still pending (e.g. TIP) but the frontend timeout already passed.
        // Treat it as unconfirmed/failed rather than showing a retry page.
        $stmt = mysqli_prepare($conn, "UPDATE payments SET status = 'failed' WHERE transaction_id = ?");
        mysqli_stmt_bind_param($stmt, "s", $localPayment['transaction_id']);
        mysqli_stmt_execute($stmt);

        $role = getUserRole();
        $target = ($role === 'farmer') ? '../farmer/dashboard.php' : '../buyer/dashboard.php';
        $timeoutReason = "Payment unconfirmed. You may have ignored the prompt or it timed out.";
        header("Location: " . $target . "?payment=failed&reason=" . urlencode($timeoutReason));
        exit;
    }
}
?>