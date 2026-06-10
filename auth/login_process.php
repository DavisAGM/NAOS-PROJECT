<?php
header('Content-Type: application/json');
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

$data = json_decode(file_get_contents('php://input'), true);

$username = mysqli_real_escape_string($conn, $data['username'] ?? $_POST['username'] ?? '');
$password = $data['password'] ?? $_POST['password'] ?? '';
$lang = $data['lang'] ?? $_POST['lang'] ?? 'en';

// Check if id_verification_status column exists (added by migration)
$verColCheck = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'id_verification_status'");
$hasVerCol = mysqli_num_rows($verColCheck) > 0;

$selectFields = "id, password_hash, role, is_active, is_phone_verified, is_profile_complete, phone_number, lang" . ($hasVerCol ? ", id_verification_status" : "");
$stmt = mysqli_prepare($conn, "SELECT $selectFields FROM users WHERE username = ?");

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Security: Check Maintenance Mode
$mRes = mysqli_query($conn, "SELECT setting_value FROM system_settings WHERE setting_key = 'maintenance_mode'");
$maintenance = mysqli_fetch_assoc($mRes);
if ($maintenance && $maintenance['setting_value'] == '1') {
    if (!$user || $user['role'] !== 'admin') {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'The system is currently undergoing maintenance. Please try again later.']);
        exit();
    }
}

if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true); // Prevent session fixation

    if ((int) $user['is_active'] === 0) {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Your account has been deactivated. Please contact an administrator.']);
        exit();
    }

    // Check ID Verification Status
    $verStatus = $user['id_verification_status'] ?? 'approved';
    if ($verStatus === 'pending') {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Your account is currently under review. You will receive an SMS notification once your verification is complete.']);
        exit();
    }
    if ($verStatus === 'rejected') {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Your account verification was unsuccessful. Please contact support for assistance.']);
        exit();
    }


    // Check Phone Verification
    if ((int) $user['is_phone_verified'] === 0) {
        ob_clean();
        echo json_encode(['success' => true, 'redirect' => 'verify_phone.php', 'phone' => $user['phone_number']]);
        exit();
    }

    // Check Profile Completion
    if ((int) $user['is_profile_complete'] === 0) {
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['role']          = $user['role'];
        $_SESSION['active_role']   = $user['role']; // ensure getActiveRole() works immediately
        $_SESSION['lang']          = $user['lang'] ?? $lang;
        ob_clean();
        // Login successful BUT redirect to profile completion
        echo json_encode(['success' => true, 'redirect' => 'complete_profile.php']);
        exit();
    }

    // Fetch roles from user_roles
    $roleStmt = mysqli_prepare($conn, "SELECT role_type FROM user_roles WHERE user_id = ? ORDER BY is_primary DESC LIMIT 1");
    mysqli_stmt_bind_param($roleStmt, "i", $user['id']);
    mysqli_stmt_execute($roleStmt);
    $roleResult = mysqli_stmt_get_result($roleStmt);
    $roleRow = mysqli_fetch_assoc($roleResult);

    // Fallback to legacy role if no user_roles entry (though migration should have fixed this)
    $mappedRole = $roleRow ? $roleRow['role_type'] : $user['role'];

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['role']        = $mappedRole;
    $_SESSION['active_role'] = $mappedRole; // ensure getActiveRole() works immediately
    $_SESSION['lang']        = $user['lang'] ?? $lang;
    ob_clean(); // Discard any prior output/warnings
    echo json_encode(['success' => true, 'role' => $mappedRole, 'user_id' => $user['id']]);
} else {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid Username or Password.']);
}
?>