<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Please log in to see your reports.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT date, CONCAT(activity_type, ' ', crop_name) as activity, quantity as yield, 0 as income, 0 as expense FROM farm_activities WHERE user_id = ? ORDER BY date DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$records = [];
$total_income = 0;
$total_expense = 0;
$total_yield = 0;
$count = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $records[] = $row;
    $total_income += $row['income'];
    $total_yield += $row['yield'];
    $total_expense += $row['expense'];
    $count++;
}


$profit = $total_income - $total_expense;
$avg_yield = $count > 0 ? round($total_yield / $count, 2) : 0;
$usage_report = "Total Income: MWK $total_income | Total Expense: MWK $total_expense | Profit: MWK $profit | Avg Yield: $avg_yield";

echo json_encode(['records' => $records, 'usage_report' => $usage_report]);
?>