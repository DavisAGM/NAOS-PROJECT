<?php
header('Content-Type: application/json');
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/sms_helper.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$userId = (int)($_POST['user_id'] ?? $_GET['user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $colCheck = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'id_verification_status'");
    if (mysqli_num_rows($colCheck) === 0) {
        echo json_encode([
            'success' => true,
            'users' => [],
            'pending_count' => 0,
            '_note' => 'Run migration_user_verification.sql first'
        ]);
        exit();
    }

    $status = $_GET['status'] ?? 'pending';
    $allowed = ['pending', 'approved', 'rejected'];
    if (!in_array($status, $allowed)) $status = 'pending';

    $stmt = mysqli_prepare($conn,
        "SELECT u.id, u.username, u.phone_number, u.role, u.national_id_path,
                u.id_verification_status, u.id_verification_notes, u.created_at, u.farmer_id, u.location, u.gender
         FROM users u
         WHERE LOWER(u.id_verification_status) = LOWER(?)
           AND LOWER(u.role) LIKE '%farmer%'
           AND u.role != 'admin'
         ORDER BY u.created_at DESC");
    mysqli_stmt_bind_param($stmt, 's', $status);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    $countRes = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE id_verification_status = 'pending' AND LOWER(role) LIKE '%farmer%' AND role != 'admin'");
    $countRow = mysqli_fetch_assoc($countRes);

    echo json_encode(['success' => true, 'users' => $users, 'pending_count' => (int)$countRow['cnt']]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'view_id') {
    $stmt = mysqli_prepare($conn, "SELECT national_id_path FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user || !$user['national_id_path']) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No ID document found']);
        exit();
    }

    $filePath = __DIR__ . '/../' . $user['national_id_path'];
    if (!file_exists($filePath)) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'File not found on server']);
        exit();
    }

    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'pdf'  => 'application/pdf',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
    ];
    $mime = $mimeTypes[$ext] ?? 'application/octet-stream';

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: no-store');
    readfile($filePath);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

if ($action === 'approve') {
    $stmt = mysqli_prepare($conn, "SELECT phone_number, username FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit();
    }

    $upd = mysqli_prepare($conn, "UPDATE users SET id_verification_status = 'approved', id_verification_notes = NULL WHERE id = ?");
    mysqli_stmt_bind_param($upd, 'i', $userId);
    mysqli_stmt_execute($upd);

    $logMsg = "Admin approved user verification for: {$user['username']} (ID: $userId)";
    $logStmt = mysqli_prepare($conn, "INSERT INTO logs (message, user_id) VALUES (?, ?)");
    $adminId = $_SESSION['user_id'];
    mysqli_stmt_bind_param($logStmt, 'si', $logMsg, $adminId);
    mysqli_stmt_execute($logStmt);

    $smsMessage = "Dear {$user['username']}, your account review was successful. You can now visit our NAOS platform to enjoy our services. Welcome!";
    sendCustomSMS($user['phone_number'], $smsMessage);

    echo json_encode(['success' => true, 'message' => "User approved and SMS notification sent."]);

} elseif ($action === 'reject') {
    $notes = trim($_POST['notes'] ?? '');

    $stmt = mysqli_prepare($conn, "SELECT phone_number, username FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit();
    }

    $upd = mysqli_prepare($conn, "UPDATE users SET id_verification_status = 'rejected', id_verification_notes = ? WHERE id = ?");
    mysqli_stmt_bind_param($upd, 'si', $notes, $userId);
    mysqli_stmt_execute($upd);

    $logMsg = "Admin rejected user verification for: {$user['username']} (ID: $userId). Notes: $notes";
    $logStmt = mysqli_prepare($conn, "INSERT INTO logs (message, user_id) VALUES (?, ?)");
    $adminId = $_SESSION['user_id'];
    mysqli_stmt_bind_param($logStmt, 'si', $logMsg, $adminId);
    mysqli_stmt_execute($logStmt);

    $smsMessage = "Dear {$user['username']}, your NAOS account verification was unsuccessful. " . ($notes ? "Reason: $notes. " : "") . "Please contact support for assistance.";
    sendCustomSMS($user['phone_number'], $smsMessage);

    echo json_encode(['success' => true, 'message' => "User rejected and notified via SMS."]);

} else {
    echo json_encode(['success' => false, 'error' => 'Unknown action']);
}

/**
 * Send a custom (non-verification) SMS using existing SMS8 setup.
 */
function sendCustomSMS($phone, $message)
{
    global $conn;
    $sms8_api_key  = 'b1b85f8d52e563b502519d81ff974eafd840c73f';
    $sms8_device_id = '10609';
    $sms8_sim_slot  = '0';

    $result = sendSMS8($phone, $message, $sms8_api_key, $sms8_device_id, $sms8_sim_slot);
    logSMS($conn, $phone, $message, $result['success'], 'SMS8', $result['error'] ?? null);
    return $result['success'];
}
?>
