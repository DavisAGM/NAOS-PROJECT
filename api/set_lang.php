<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php'; // handles session_start safely

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$lang = $data['lang'] ?? $_POST['lang'] ?? '';

$allowed = ['en', 'ny', 'tum'];
if (in_array($lang, $allowed)) {
    $_SESSION['lang'] = $lang;
    
    // Persist to database for logged-in users
    if (isset($_SESSION['user_id'])) {
        $stmt = mysqli_prepare($conn, "UPDATE users SET lang = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $lang, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
    }
    
    echo json_encode(['success' => true, 'lang' => $lang]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid language. Allowed: ' . implode(', ', $allowed)]);
}
