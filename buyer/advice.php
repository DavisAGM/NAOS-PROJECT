<?php
header('Content-Type: application/json');
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

/**
 * Dynamic Buying Recommendations
 * Analyzes price trends and listing volumes to provide actionable advice to buyers.
 */

if (!isLoggedIn()) {
    echo json_encode(['advice' => 'Please log in to see recommendations.']);
    exit;
}

$adviceItems = [];

// 1. Analyze Price Trends (Current vs Previous recorded price)
$trendQuery = "
    SELECT cc.crop_name, cc.current_price, 
    (SELECT price_per_kg FROM market_price_history mph WHERE mph.crop_name = cc.crop_name ORDER BY recorded_at DESC LIMIT 1 OFFSET 1) as prev_price
    FROM crop_config cc
    WHERE cc.current_price IS NOT NULL
";

$res = mysqli_query($conn, $trendQuery);
$drops = [];
$hikes = [];

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $name = $row['crop_name'];
        $curr = (float)$row['current_price'];
        $prev = (float)$row['prev_price'];

        if ($prev > 0) {
            $diffPercent = (($curr - $prev) / $prev) * 100;
            if ($diffPercent < -1.5) {
                $drops[] = [
                    'name' => ucfirst($name),
                    'percent' => round(abs($diffPercent), 1)
                ];
            } elseif ($diffPercent > 1.5) {
                $hikes[] = [
                    'name' => ucfirst($name),
                    'percent' => round($diffPercent, 1)
                ];
            }
        }
    }
}

// Add advice for price drops (positive news first)
foreach ($drops as $d) {
    $adviceItems[] = "Good news! <strong>{$d['name']}</strong> prices have dropped by approximately {$d['percent']}%. It's an excellent time to restock.";
}

// 2. Analyze Supply Levels (Based on active listings)
$supplyQuery = "
    SELECT cc.crop_name, COUNT(pl.id) as listing_count 
    FROM produce_listings pl 
    JOIN crop_config cc ON pl.crop_id = cc.id 
    WHERE pl.status = 'active' 
    GROUP BY cc.id 
    ORDER BY listing_count DESC 
    LIMIT 3
";

$res = mysqli_query($conn, $supplyQuery);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        if ($row['listing_count'] >= 3) {
            $name = ucfirst($row['crop_name']);
            $adviceItems[] = "We're seeing a high volume of <strong>{$name}</strong> listings today. High availability often means better opportunities for bulk discounts.";
        }
    }
}

// Add warnings for price hikes
foreach ($hikes as $h) {
    $adviceItems[] = "Market alert: <strong>{$h['name']}</strong> prices are trending upwards ({$h['percent']}% increase). You might want to secure your purchase before further hikes.";
}

// 3. Seasonal Logic (Based on Malawi's typical agricultural cycles)
$month = (int)date('m');
if ($month >= 4 && $month <= 6) {
    // Main harvest season
    $adviceItems[] = "It is currently the peak harvest season in many districts. Look out for the freshest produce at competitive prices.";
} elseif ($month >= 12 || $month <= 2) {
    // Lean season
    $adviceItems[] = "We are currently in the lean season. Prices for staple grains may be volatile; consider focusing on drought-resistant alternatives.";
}

// 4. Default / General Best Practices
$adviceItems[] = "Always check the 'Available Produce' section frequently as new listings from local farmers are added daily.";
$adviceItems[] = "Consider using our 'Bulk Order' feature if you are buying for a business to save on unit costs.";

// Shuffle items slightly for variety, but keep top news at the top
$topNews = array_slice($adviceItems, 0, 2);
$others = array_slice($adviceItems, 2);
shuffle($others);
$finalAdvice = array_merge($topNews, array_slice($others, 0, 3)); // Show max 5 items

$html = "<strong>Based on real-time market analysis:</strong><br><br>";
$html .= "<ul style='padding-left: 20px; margin: 0;'>";
foreach ($finalAdvice as $item) {
    $html .= "<li style='margin-bottom: 10px; line-height: 1.4; color: #374151;'>$item</li>";
}
$html .= "</ul>";

echo json_encode(['advice' => $html]);
