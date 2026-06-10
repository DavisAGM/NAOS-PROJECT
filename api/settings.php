<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$role = getUserRole();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $keys = $_GET['keys'] ?? 'subscription_amount';
    $keyList = explode(',', $keys);
    $placeholders = implode(',', array_fill(0, count($keyList), '?'));

    $query = "SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ($placeholders)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, str_repeat('s', count($keyList)), ...$keyList);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $settings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    // Add default if missing
    if (!isset($settings['subscription_amount'])) {
        $settings['subscription_amount'] = '5000';
    }

    echo json_encode(['success' => true, 'settings' => $settings]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($role !== 'admin') {
        echo json_encode(['error' => 'Only admins can update settings']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['error' => 'Invalid data']);
        exit;
    }

    foreach ($data as $key => $value) {
        $stmt = mysqli_prepare($conn, "INSERT INTO system_settings (setting_key, setting_value, updated_by) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = ?, updated_by = ?");
        mysqli_stmt_bind_param($stmt, "ssisi", $key, $value, $userId, $value, $userId);
        mysqli_stmt_execute($stmt);
    }

    echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
    exit;
}
