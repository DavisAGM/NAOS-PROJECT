<?php
/**
 * Role Switcher API
 * Allows users with multiple roles to switch between them
 */
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];
$newRole = $data['role'] ?? '';

// Validate role
if (!in_array($newRole, ['farmer', 'buyer'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid role']);
    exit;
}

// Check if user has this role
if (!hasRole($conn, $userId, $newRole)) {
    echo json_encode(['success' => false, 'error' => 'You do not have access to this role']);
    exit;
}

// Switch active role
if (switchActiveRole($conn, $userId, $newRole)) {
    // Determine redirect URL
    $redirectUrl = ($newRole === 'farmer') ? '/naos/farmer/dashboard.php' : '/naos/buyer/dashboard.php';

    echo json_encode([
        'success' => true,
        'message' => 'Role switched successfully',
        'new_role' => $newRole,
        'redirect' => $redirectUrl
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to switch role']);
}
?>