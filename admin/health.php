<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM logs ORDER BY timestamp DESC LIMIT 50");
$logs = mysqli_fetch_all($result, MYSQLI_ASSOC);

$uptime = shell_exec('uptime'); // Basic server uptime

echo json_encode(['logs' => $logs, 'uptime' => $uptime]);
?>