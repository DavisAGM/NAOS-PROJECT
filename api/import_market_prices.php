<?php
/**
 * NAOS — Bulk Market Price Import
 * 
 * Allows admins to upload a CSV file with crop prices as a fallback
 * when external APIs (WFP/HDX) are unavailable.
 * 
 * Expected CSV format:
 * crop, price
 * maize, 850
 * beans, 1200
 */

header('Content-Type: application/json');
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file']['tmp_name'];
$handle = fopen($file, 'r');
if (!$handle) {
    echo json_encode(['success' => false, 'error' => 'Could not read file']);
    exit;
}

// Skip header row
$header = fgetcsv($handle);

$updated = 0;
$errors = [];
$skipped = [];

while (($row = fgetcsv($handle)) !== false) {
    if (count($row) < 2) continue;

    $cropName = trim($row[0]);
    $price = (float) trim($row[1]);

    if (!$cropName || $price <= 0) {
        $skipped[] = $cropName ?: 'Empty row';
        continue;
    }

    // Update crop_config
    $stmt = mysqli_prepare($conn, "UPDATE crop_config SET current_price = ?, price_updated_at = NOW() WHERE LOWER(crop_name) = LOWER(?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ds', $price, $cropName);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $updated++;
            
            // Log in history
            $histStmt = mysqli_prepare($conn, "INSERT INTO market_price_history (crop_name, price_per_kg, source, recorded_at) VALUES (?, ?, 'manual_bulk', NOW())");
            if ($histStmt) {
                mysqli_stmt_bind_param($histStmt, 'sd', $cropName, $price);
                mysqli_stmt_execute($histStmt);
            }
        } else {
            $errors[] = "Crop '{$cropName}' not found in system.";
        }
    }
}

fclose($handle);

// Log the import in wfp_sync_log (hijacking for tracking)
$logMsg = "Bulk Import: {$updated} crops updated. Errors: " . implode(', ', array_slice($errors, 0, 5));
$logStmt = mysqli_prepare($conn, "INSERT INTO wfp_sync_log (synced_at, source, records_found, crops_updated, status, message) VALUES (NOW(), 'Manual Bulk Import', ?, ?, 'success', ?)");
mysqli_stmt_bind_param($logStmt, 'iis', $updated, $updated, $logMsg);
mysqli_stmt_execute($logStmt);

echo json_encode([
    'success' => true,
    'message' => "Successfully updated {$updated} crop prices.",
    'errors' => $errors,
    'updated_count' => $updated
]);
?>
