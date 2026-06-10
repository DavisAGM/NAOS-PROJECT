<?php
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT id, date, CONCAT(activity_type, ' ', crop_name) as activity, notes as input_usage, quantity as yield, 0 as expense, 0 as income FROM farm_activities WHERE user_id = $user_id");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="farm_records_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Date', 'Activity', 'Notes/Inputs', 'Yield/Qty', 'Expense', 'Income']);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [$row['id'], $row['date'], $row['activity'], $row['input_usage'], $row['yield'], $row['expense'], $row['income']]);
}

?>