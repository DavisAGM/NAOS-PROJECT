<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'profile') {
    $userId = $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "
        SELECT u.id, u.username, u.phone_number, u.role, u.gender, u.location, u.profile_picture, u.subscription_status, u.notification_preferences,
               (SELECT expiry_date FROM subscriptions WHERE user_id = u.id AND status = 'active' ORDER BY id DESC LIMIT 1) as subscription_expiry
        FROM users u 
        WHERE u.id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $user['notification_preferences'] = json_decode($user['notification_preferences'], true) ?: [
            'sms_alerts' => true,
            'app_notifications' => true,
            'marketing' => false
        ];
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $userId = $_SESSION['user_id'];
    $username = trim($_POST['username'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $gender = $_POST['gender'] ?? 'male';
    $notif_prefs = $_POST['notification_preferences'] ?? null;

    if (empty($username)) {
        echo json_encode(['error' => 'Username is required']);
        exit;
    }

    if (strlen($username) < 8) {
        echo json_encode(['error' => 'Username must be at least 8 characters long']);
        exit;
    }

    // Handle Profile Picture Upload
    $profilePic = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../assets/images/profiles/";
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $fileExt = strtolower(pathinfo($_FILES['profile_picture']['name'], INFO_EXTENSION));
        $newFileName = "profile_" . $userId . "_" . time() . "." . $fileExt;
        $targetFile = $targetDir . $newFileName;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
            $profilePic = $newFileName;
        }
    }

    $params = [$username, $location, $gender];
    $types = "sss";
    $sql = "UPDATE users SET username = ?, location = ?, gender = ?";
    
    if ($profilePic) {
        $sql .= ", profile_picture = ?";
        $params[] = $profilePic;
        $types .= "s";
    }
    
    if ($notif_prefs) {
        $sql .= ", notification_preferences = ?";
        $params[] = $notif_prefs; // Assume it comes as JSON string from client
        $types .= "s";
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $userId;
    $types .= "i";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);

    if (mysqli_stmt_execute($stmt)) {
        logActivity($conn, "Updated profile details", $userId);
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully', 'profile_picture' => $profilePic]);
    } else {
        echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    }
    exit;
}

$role = $_GET['role'] ?? 'buyer';
$status = $_GET['status'] ?? 1;

$stmt = mysqli_prepare($conn, "SELECT id, username, location, is_active FROM users WHERE role = ? AND is_active = ?");
mysqli_stmt_bind_param($stmt, "si", $role, $status);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = [
        'id' => $row['id'],
        'username' => $row['username'],
        'location' => $row['location']
    ];
}

echo json_encode(['success' => true, 'users' => $users]);
?>