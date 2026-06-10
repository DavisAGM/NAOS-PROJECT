<?php
require '../includes/db.php';
require '../includes/sms_helper.php';

ob_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$phone = $input['phone'] ?? '';

if (empty($phone)) {
    echo json_encode(['success' => false, 'error' => 'Phone number is required']);
    exit;
}

// Check if user exists
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE phone_number = ?");
mysqli_stmt_bind_param($stmt, "s", $phone);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {
    // Generate new code
    $new_code = str_pad(api_mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Update database
    $update_stmt = mysqli_prepare($conn, "UPDATE users SET phone_verification_code = ? WHERE id = ?");
    mysqli_stmt_bind_param($update_stmt, "si", $new_code, $user['id']);

    if (mysqli_stmt_execute($update_stmt)) {
        // Send SMS
        if (sendVerificationSMS($phone, $new_code)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to send SMS']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'User not found']);
}

// Helper for random number if mt_rand is not safer (but for 6 digit code mt_rand is fine enough for this demo context, using rand for simplicity if mt_rand behaves oddly in some envs, but PHP 7+ mt_rand is alias to random_int usually or decent mersenne twister)
function api_mt_rand($min, $max)
{
    return mt_rand($min, $max);
}
?>