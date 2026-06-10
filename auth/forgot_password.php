<?php
require '../includes/db.php';
require '../includes/sms_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// Send verification code to phone
if ($action === 'send_code') {
    $phone = $input['phone'] ?? '';

    if (empty($phone)) {
        echo json_encode(['success' => false, 'error' => 'Phone number is required']);
        exit;
    }

    if (!preg_match('/^0\d{9}$/', $phone)) {
        echo json_encode(['success' => false, 'error' => 'Phone number must be exactly 10 digits and start with 0']);
        exit;
    }

    // Check if user exists with this phone number
    $stmt = mysqli_prepare($conn, "SELECT id, username FROM users WHERE phone_number = ?");
    mysqli_stmt_bind_param($stmt, "s", $phone);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // Generate new 6-digit code
        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update user's verification code
        $update_stmt = mysqli_prepare($conn, "UPDATE users SET phone_verification_code = ? WHERE id = ?");
        mysqli_stmt_bind_param($update_stmt, "si", $code, $user['id']);

        if (mysqli_stmt_execute($update_stmt)) {
            // Send SMS
            if (sendVerificationSMS($phone, $code)) {
                echo json_encode(['success' => true, 'message' => 'Verification code sent to your phone']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to send SMS']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
    } else {
        // Don't reveal that user doesn't exist for security
        echo json_encode(['success' => false, 'error' => 'There\'s no account with the entered phone number']);
    }
}

// Verify code
elseif ($action === 'verify_code') {
    $phone = $input['phone'] ?? '';
    $code = $input['code'] ?? '';

    if (empty($phone) || empty($code)) {
        echo json_encode(['success' => false, 'error' => 'Phone number and code are required']);
        exit;
    }

    // Verify code matches
    $stmt = mysqli_prepare($conn, "SELECT id, username FROM users WHERE phone_number = ? AND phone_verification_code = ?");
    mysqli_stmt_bind_param($stmt, "ss", $phone, $code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'message' => 'Code verified successfully', 'username' => $user['username']]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid verification code']);
    }
}

//  Reset password
elseif ($action === 'reset_password') {
    $phone = $input['phone'] ?? '';
    $code = $input['code'] ?? '';
    $new_password = $input['new_password'] ?? '';

    if (empty($phone) || empty($code) || empty($new_password)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }

    if (strlen($new_password) < 6) {
        echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
        exit;
    }

    if (!preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        echo json_encode(['success' => false, 'error' => 'Password must contain both letters and numbers']);
        exit;
    }

    // Verify code one more time before resetting password
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE phone_number = ? AND phone_verification_code = ?");
    mysqli_stmt_bind_param($stmt, "ss", $phone, $code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // Hash the new password
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password and clear verification code
        $update_stmt = mysqli_prepare($conn, "UPDATE users SET password_hash = ?, phone_verification_code = NULL WHERE id = ?");
        mysqli_stmt_bind_param($update_stmt, "si", $password_hash, $user['id']);

        if (mysqli_stmt_execute($update_stmt)) {
            echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update password']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid verification code']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>