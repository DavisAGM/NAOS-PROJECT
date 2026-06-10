<?php
header('Content-Type: application/json');
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $stmt = mysqli_prepare($conn, "SELECT id, crop_name, min_rainfall, max_rainfall, min_temp, max_temp, drought_resistant, current_price FROM crop_config ORDER BY crop_name ASC");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $crops = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $crops[] = $row;
    }
    echo json_encode(['success' => true, 'crops' => $crops]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $crop_name = strtolower(trim($_POST['crop_name'] ?? ''));
        $min_rainfall = (int)($_POST['min_rainfall'] ?? 0);
        $max_rainfall = (int)($_POST['max_rainfall'] ?? 9999);
        $min_temp = (float)($_POST['min_temp'] ?? 0);
        $max_temp = (float)($_POST['max_temp'] ?? 99);
        $drought_resistant = (int)($_POST['drought_resistant'] ?? 0);
        $current_price = (float)($_POST['current_price'] ?? 0);

        if (empty($crop_name)) {
            echo json_encode(['success' => false, 'error' => 'Crop name is required']);
            exit();
        }

        if ($action === 'add') {
            // Check if exists
            $check = mysqli_prepare($conn, "SELECT id FROM crop_config WHERE crop_name = ?");
            mysqli_stmt_bind_param($check, "s", $crop_name);
            mysqli_stmt_execute($check);
            if (mysqli_num_rows(mysqli_stmt_get_result($check)) > 0) {
                echo json_encode(['success' => false, 'error' => 'Crop already exists']);
                exit();
            }

            $stmt = mysqli_prepare($conn, "INSERT INTO crop_config (crop_name, min_rainfall, max_rainfall, min_temp, max_temp, drought_resistant, current_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "siiddid", $crop_name, $min_rainfall, $max_rainfall, $min_temp, $max_temp, $drought_resistant, $current_price);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Crop added successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database error']);
            }
        } else {
            // Update
            $stmt = mysqli_prepare($conn, "UPDATE crop_config SET crop_name = ?, min_rainfall = ?, max_rainfall = ?, min_temp = ?, max_temp = ?, drought_resistant = ?, current_price = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "siiddidi", $crop_name, $min_rainfall, $max_rainfall, $min_temp, $max_temp, $drought_resistant, $current_price, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Crop updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database error']);
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, "DELETE FROM crop_config WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Crop deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
}
?>
