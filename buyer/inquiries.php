<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn() || getUserRole() !== 'buyer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$buyer_id = $_SESSION['user_id'];
$listing_id = $data['listing_id'] ?? $_POST['listing_id'];
$message = $data['message'] ?? $_POST['message'];

$stmt = mysqli_prepare($conn, "INSERT INTO inquiries (buyer_id, listing_id, message, date) VALUES (?, ?, ?, NOW())");
mysqli_stmt_bind_param($stmt, "iis", $buyer_id, $listing_id, $message);
mysqli_stmt_execute($stmt);

// Add Notification for Seller
// 1. Get Seller ID
$sellerQ = mysqli_query($conn, "SELECT user_id FROM produce_listings WHERE id = $listing_id");
if ($sellerQ && mysqli_num_rows($sellerQ) > 0) {
    $seller = mysqli_fetch_assoc($sellerQ);
    $seller_id = $seller['user_id'];
    
    // 2. Insert Notification
    $notifMsg = "New inquiry received for your listing.";
    $notifStmt = mysqli_prepare($conn, "INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    mysqli_stmt_bind_param($notifStmt, "is", $seller_id, $notifMsg);
    mysqli_stmt_execute($notifStmt);
}

echo json_encode(['success' => true]);
?>
