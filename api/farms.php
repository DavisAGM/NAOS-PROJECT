<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn() || getUserRole() !== 'farmer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_REQUEST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_farms') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM farms WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $farms = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $farms[] = $row;
    }
    echo json_encode(['success' => true, 'farms' => $farms]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_farm') {
    $farm_name = trim($_POST['farm_name'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $ecological_zone = trim($_POST['ecological_zone'] ?? '');
    $soil_type = trim($_POST['soil_type'] ?? '');
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;

    if (empty($farm_name) || empty($district) || empty($soil_type) || !$latitude || !$longitude) {
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO farms (user_id, farm_name, district, ecological_zone, soil_type, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issssdd", $user_id, $farm_name, $district, $ecological_zone, $soil_type, $latitude, $longitude);
    
    if (mysqli_stmt_execute($stmt)) {
        logActivity($conn, "Added a new farm: $farm_name", $user_id);
        echo json_encode(['success' => true, 'message' => 'Farm added successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to add farm: ' . mysqli_error($conn)]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete_farm') {
    $farm_id = $_POST['farm_id'] ?? null;

    if (!$farm_id) {
        echo json_encode(['error' => 'Farm ID is required.']);
        exit;
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM farms WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $farm_id, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        logActivity($conn, "Deleted farm ID: $farm_id", $user_id);
        echo json_encode(['success' => true, 'message' => 'Farm deleted successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to delete farm.']);
    }
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
