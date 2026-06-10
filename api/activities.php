<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $query = "
        SELECT a.id, a.crop_name, a.activity_type, a.date, a.quantity, a.unit, a.notes, f.farm_name, a.farm_id
        FROM farm_activities a
        LEFT JOIN farms f ON a.farm_id = f.id
        WHERE a.user_id = ?
        ORDER BY a.date DESC, a.id DESC
        LIMIT 50
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $activities = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $activities[] = [
            'id' => $row['id'],
            'crop_name' => ucwords($row['crop_name']),
            'activity_type' => $row['activity_type'],
            'date' => $row['date'],
            'quantity' => $row['quantity'],
            'unit' => $row['unit'],
            'notes' => $row['notes'],
            'farm_id' => $row['farm_id'],
            'farm_name' => $row['farm_name'] ?: 'Main Farm'
        ];
    }

    echo json_encode(['success' => true, 'activities' => $activities]);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? 'add';

    if ($action === 'delete') {
        $id = intval($data['id']);
        $stmt = mysqli_prepare($conn, "DELETE FROM farm_activities WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Activity deleted']);
        } else {
            echo json_encode(['error' => 'Delete failed']);
        }
        exit;
    }

    if ($action === 'update' || $action === 'add') {
        $crop_name = mysqli_real_escape_string($conn, $data['crop_name']);
        $activity_type = mysqli_real_escape_string($conn, $data['activity_type']);
        $date = mysqli_real_escape_string($conn, $data['date']);
        $quantity = isset($data['quantity']) ? floatval($data['quantity']) : null;
        $unit = mysqli_real_escape_string($conn, $data['unit'] ?? '');
        $notes = mysqli_real_escape_string($conn, $data['notes'] ?? '');
        $farm_id = intval($data['farm_id']);

        if ($action === 'update') {
            $id = intval($data['id']);
            $stmt = mysqli_prepare($conn, "UPDATE farm_activities SET farm_id=?, crop_name=?, activity_type=?, date=?, quantity=?, unit=?, notes=? WHERE id=? AND user_id=?");
            mysqli_stmt_bind_param($stmt, "isssdssii", $farm_id, $crop_name, $activity_type, $date, $quantity, $unit, $notes, $id, $user_id);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO farm_activities (user_id, farm_id, crop_name, activity_type, date, quantity, unit, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iisssdss", $user_id, $farm_id, $crop_name, $activity_type, $date, $quantity, $unit, $notes);
        }

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Activity saved']);
        } else {
            echo json_encode(['error' => 'Save failed: ' . mysqli_error($conn)]);
        }
        exit;
    }
}
?>
