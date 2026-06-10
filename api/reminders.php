<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Standard timelines for common crops in Malawi
// Format: Days after planting => ['Activity Type', 'Description']
$crop_schedules = [
    'maize' => [
        14 => ['weeded', 'First weeding'],
        21 => ['fertilized', 'Apply basal fertilizer (NPK)'],
        42 => ['weeded', 'Second weeding'],
        56 => ['fertilized', 'Apply top dressing (Urea)'],
        120 => ['harvested', 'Expected harvest time']
    ],
    'groundnut' => [
        21 => ['weeded', 'First weeding before pegging'],
        35 => ['weeded', 'Second weeding'],
        45 => ['sprayed', 'Apply fungicide/pesticide if needed'],
        110 => ['harvested', 'Check maturity before full harvest']
    ],
    'groundnuts' => [ // Alias for plural
        21 => ['weeded', 'First weeding before pegging'],
        35 => ['weeded', 'Second weeding'],
        45 => ['sprayed', 'Apply fungicide/pesticide if needed'],
        110 => ['harvested', 'Check maturity before full harvest']
    ],
    'soybeans' => [
        14 => ['weeded', 'First weeding'],
        30 => ['weeded', 'Second weeding'],
        45 => ['sprayed', 'Scout for pests and spray if necessary'],
        105 => ['harvested', 'Harvest when pods turn brown']
    ],
    'tobacco' => [
        7 => ['fertilized', 'Basal fertilizer application'],
        21 => ['weeded', 'First weeding and banking'],
        28 => ['sprayed', 'Apply pesticide/fungicide'],
        42 => ['fertilized', 'Top dressing fertilizer'],
        80 => ['harvested', 'Start reaping lower leaves']
    ],
    'rice' => [
        21 => ['weeded', 'First weeding/Hand pulling'],
        30 => ['fertilized', 'First urea application'],
        50 => ['weeded', 'Second weeding'],
        60 => ['fertilized', 'Second urea application'],
        135 => ['harvested', 'Harvest when 80% of grains turn straw-colored']
    ],
    'beans' => [
        21 => ['weeded', 'First weeding'],
        35 => ['sprayed', 'Apply pesticide for aphids/bean flies'],
        42 => ['weeded', 'Second weeding if necessary'],
        85 => ['harvested', 'Harvest when pods are dry and brittle']
    ],
    'cassava' => [
        30 => ['weeded', 'First weeding'],
        60 => ['weeded', 'Second weeding'],
        90 => ['weeded', 'Third weeding'],
        300 => ['harvested', 'Check for root maturity (usually 10-12 months)']
    ],
    'cowpeas' => [
        21 => ['weeded', 'First weeding'],
        40 => ['sprayed', 'Apply pesticide for pod borers'],
        80 => ['harvested', 'Harvest when pods are dry']
    ],
    'millet' => [
        14 => ['weeded', 'First weeding'],
        42 => ['weeded', 'Second weeding/Thinning'],
        120 => ['harvested', 'Harvest when heads turn brown']
    ],
    'sorghum' => [
        14 => ['weeded', 'First weeding'],
        42 => ['weeded', 'Second weeding'],
        125 => ['harvested', 'Harvest when seeds are hard']
    ],
    'pigeon peas' => [
        30 => ['weeded', 'First weeding'],
        60 => ['weeded', 'Second weeding'],
        180 => ['harvested', 'Harvest when pods are dry and rattle']
    ]
];

// 1. Get the most recent "planted" activity for each crop on each farm
$planted_query = "
    SELECT id, farm_id, crop_name, date as planting_date
    FROM farm_activities
    WHERE user_id = ? AND activity_type = 'planted'
    AND (COALESCE(farm_id, 0), crop_name, date) IN (
        SELECT COALESCE(farm_id, 0), crop_name, MAX(date)
        FROM farm_activities
        WHERE user_id = ? AND activity_type = 'planted'
        GROUP BY COALESCE(farm_id, 0), crop_name
    )
";

$stmt = mysqli_prepare($conn, $planted_query);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($stmt);
$planted_result = mysqli_stmt_get_result($stmt);

$reminders = [];
$current_date = new DateTime();

while ($row = mysqli_fetch_assoc($planted_result)) {
    $farm_id = $row['farm_id'];
    $crop_name = strtolower(trim($row['crop_name']));
    $planting_date = new DateTime($row['planting_date']);
    
    if (isset($crop_schedules[$crop_name])) {
        $schedule = $crop_schedules[$crop_name];
        
        // 2. Find what activities have ALREADY been logged since planting
        $logged_query = "
            SELECT activity_type, MIN(date) as first_logged_date
            FROM farm_activities
            WHERE user_id = ? AND farm_id = ? AND crop_name = ? AND date > ?
            GROUP BY activity_type
        ";
        $logged_stmt = mysqli_prepare($conn, $logged_query);
        $db_crop_name = $row['crop_name']; // keep original casing for db query
        mysqli_stmt_bind_param($logged_stmt, "iiss", $user_id, $farm_id, $db_crop_name, $row['planting_date']);
        mysqli_stmt_execute($logged_stmt);
        $logged_result = mysqli_stmt_get_result($logged_stmt);
        
        $logged_activities = [];
        while ($logged_row = mysqli_fetch_assoc($logged_result)) {
            $logged_activities[] = $logged_row['activity_type'];
        }
        
        // Count how many times each activity has been logged to handle multiple occurrences (like weeded)
        $logged_counts_query = "
            SELECT activity_type, COUNT(*) as count
            FROM farm_activities
            WHERE user_id = ? AND farm_id = ? AND crop_name = ? AND date > ?
            GROUP BY activity_type
        ";
        $counts_stmt = mysqli_prepare($conn, $logged_counts_query);
        mysqli_stmt_bind_param($counts_stmt, "iiss", $user_id, $farm_id, $db_crop_name, $row['planting_date']);
        mysqli_stmt_execute($counts_stmt);
        $counts_result = mysqli_stmt_get_result($counts_stmt);
        
        $activity_counts = [];
        while ($count_row = mysqli_fetch_assoc($counts_result)) {
            $activity_counts[$count_row['activity_type']] = intval($count_row['count']);
        }

        // Keep track of which activity occurrence we're checking against
        $schedule_occurrence = [];
        
        // 3. Loop through schedule and find the NEXT required activity
        foreach ($schedule as $days_after => $details) {
            $activity_type = $details[0];
            $description = $details[1];
            
            // Increment the required occurrence count for this activity
            if (!isset($schedule_occurrence[$activity_type])) {
                $schedule_occurrence[$activity_type] = 1;
            } else {
                $schedule_occurrence[$activity_type]++;
            }
            
            // Check if the user has completed this specific occurrence
            $completed_occurrences = $activity_counts[$activity_type] ?? 0;
            
            if ($completed_occurrences < $schedule_occurrence[$activity_type]) {
                // This activity is pending!
                $target_date = clone $planting_date;
                $target_date->modify("+$days_after days");
                
                $diff = $current_date->diff($target_date);
                $days_remaining = (int)$diff->format('%R%a'); // positive if in future, negative if past
                
                // Only remind if it's within a somewhat reasonable timeframe (e.g. not something from 3 years ago)
                if ($days_remaining > -365) {
                    $reminders[] = [
                        'farm_id' => $farm_id,
                        'crop_name' => ucwords($crop_name),
                        'activity_type' => $activity_type,
                        'description' => $description,
                        'target_date' => $target_date->format('Y-m-d'),
                        'days_remaining' => $days_remaining,
                        'is_overdue' => $days_remaining < 0
                    ];
                }
                
                // Once we find the NEXT pending activity for this crop, we stop adding future ones
                // to avoid overwhelming the dashboard. We just show the immediate next step.
                break; 
            }
        }
    }
}

// 4. Fetch farm names to attach to reminders
$farm_names = [];
$farms_query = "SELECT id, farm_name FROM farms WHERE user_id = ?";
$farms_stmt = mysqli_prepare($conn, $farms_query);
mysqli_stmt_bind_param($farms_stmt, "i", $user_id);
mysqli_stmt_execute($farms_stmt);
$farms_res = mysqli_stmt_get_result($farms_stmt);
while ($f_row = mysqli_fetch_assoc($farms_res)) {
    $farm_names[$f_row['id']] = $f_row['farm_name'];
}

foreach ($reminders as &$reminder) {
    if ($reminder['farm_id'] && isset($farm_names[$reminder['farm_id']])) {
        $reminder['farm_name'] = $farm_names[$reminder['farm_id']];
    } else {
        $reminder['farm_name'] = 'Unknown/Main Farm';
    }
}

// Sort reminders so the most urgent (lowest days remaining, or most overdue) appear first
usort($reminders, function($a, $b) {
    return $a['days_remaining'] <=> $b['days_remaining'];
});

echo json_encode([
    'success' => true,
    'reminders' => $reminders
]);
?>
