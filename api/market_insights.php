<?php
/**
 * Enhanced Market Intelligence API
 * Provides actionable insights, recommendations, and optimal sell windows
 */
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

/**
 * Calculate price trend over last 30 days
 * @param mysqli $conn Database connection
 * @param string $cropName Crop name
 * @return array Trend data with percentage change
 */
function calculatePriceTrend($conn, $cropName)
{
    // Get prices from last 30 days
    $stmt = mysqli_prepare($conn, "
        SELECT price_per_kg, recorded_at 
        FROM market_price_history 
        WHERE crop_name = ? 
        AND recorded_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ORDER BY recorded_at ASC
    ");
    mysqli_stmt_bind_param($stmt, "s", $cropName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $prices = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $prices[] = floatval($row['price_per_kg']);
    }

    if (count($prices) < 2) {
        return ['trend' => 'stable', 'percentage' => 0, 'direction' => '→'];
    }

    $firstPrice = $prices[0];
    $lastPrice = end($prices);
    $percentageChange = (($lastPrice - $firstPrice) / $firstPrice) * 100;

    $trend = 'stable';
    $direction = '→';

    if ($percentageChange > 5) {
        $trend = 'rising';
        $direction = '↑';
    } elseif ($percentageChange < -5) {
        $trend = 'falling';
        $direction = '↓';
    }

    return [
        'trend' => $trend,
        'percentage' => round($percentageChange, 2),
        'direction' => $direction,
        'first_price' => $firstPrice,
        'last_price' => $lastPrice
    ];
}

/**
 * Generate actionable market recommendation
 * @param string $cropName Crop name
 * @param array $trendData Trend analysis data
 * @param float $currentPrice Current market price
 * @param int $demandLevel Demand level (0-10)
 * @return string Recommendation text
 */
function generateMarketRecommendation($cropName, $trendData, $currentPrice, $demandLevel)
{
    $trend = $trendData['trend'];
    $percentage = abs($trendData['percentage']);

    if ($trend === 'rising' && $demandLevel > 5) {
        return "🔥 EXCELLENT TIME TO SELL! {$cropName} prices are rising ({$percentage}% increase) with high demand. Sell now to maximize profits.";
    } elseif ($trend === 'rising') {
        return "✅ GOOD TIME TO SELL. {$cropName} prices are trending up ({$percentage}% increase). Consider selling soon.";
    } elseif ($trend === 'falling' && $demandLevel > 5) {
        return "⚠️ SELL QUICKLY. {$cropName} prices are dropping ({$percentage}% decrease) but demand is still high. Sell before prices fall further.";
    } elseif ($trend === 'falling') {
        return "❌ HOLD IF POSSIBLE. {$cropName} prices are falling ({$percentage}% decrease). Wait for prices to stabilize unless urgent.";
    } else {
        if ($demandLevel > 7) {
            return "✅ GOOD TIME TO SELL. {$cropName} prices are stable with very high demand. Good opportunity to sell.";
        } elseif ($demandLevel > 4) {
            return "→ MODERATE OPPORTUNITY. {$cropName} prices are stable with moderate demand. Sell if you need to.";
        } else {
            return "⏳ WAIT FOR BETTER PRICES. {$cropName} has low demand currently. Consider waiting for market improvement.";
        }
    }
}

/**
 * Assess demand level for a crop
 * @param mysqli $conn Database connection
 * @param string $cropName Crop name
 * @return int Demand level (0-10)
 */
function assessDemandLevel($conn, $cropName)
{
    // Count recent inquiries
    $stmt = mysqli_prepare($conn, "
        SELECT COUNT(*) as inquiry_count
        FROM inquiries i
        JOIN produce_listings pl ON i.listing_id = pl.id
        WHERE pl.produce_type = ?
        AND i.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    mysqli_stmt_bind_param($stmt, "s", $cropName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $inquiries = mysqli_fetch_assoc($result)['inquiry_count'];

    // Count active listings
    $stmt = mysqli_prepare($conn, "
        SELECT COUNT(*) as listing_count
        FROM produce_listings
        WHERE produce_type = ?
        AND status = 'available'
    ");
    mysqli_stmt_bind_param($stmt, "s", $cropName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $listings = mysqli_fetch_assoc($result)['listing_count'];

    // Calculate demand level (0-10)
    if ($listings == 0)
        return 5; // Neutral if no data

    $ratio = $inquiries / max($listings, 1);
    $demandLevel = min(10, round($ratio * 2));

    return $demandLevel;
}

/**
 * Determine optimal sell window
 * @param array $trendData Trend analysis
 * @param int $demandLevel Demand level
 * @return string Sell window recommendation
 */
function getOptimalSellWindow($trendData, $demandLevel)
{
    $trend = $trendData['trend'];

    if ($trend === 'rising' && $demandLevel > 7) {
        return "Next 3-5 days";
    } elseif ($trend === 'rising') {
        return "Next 7-10 days";
    } elseif ($trend === 'falling') {
        return "Immediately";
    } else {
        if ($demandLevel > 6) {
            return "Next 5-7 days";
        } else {
            return "Wait 2-4 weeks";
        }
    }
}

// Main execution
$insights = [];

// Get all crops with market prices
$pricesQuery = mysqli_query($conn, "SELECT crop_name, current_price as price_per_kg FROM crop_config WHERE current_price IS NOT NULL");

while ($row = mysqli_fetch_assoc($pricesQuery)) {
    $cropName = $row['crop_name'];
    $currentPrice = floatval($row['price_per_kg']);

    // Calculate trend
    $trendData = calculatePriceTrend($conn, $cropName);

    // Assess demand
    $demandLevel = assessDemandLevel($conn, $cropName);

    // Generate recommendation
    $recommendation = generateMarketRecommendation($cropName, $trendData, $currentPrice, $demandLevel);

    // Get optimal sell window
    $sellWindow = getOptimalSellWindow($trendData, $demandLevel);

    // Determine demand level text
    if ($demandLevel >= 7) {
        $demandText = 'high';
    } elseif ($demandLevel >= 4) {
        $demandText = 'medium';
    } else {
        $demandText = 'low';
    }

    $insights[$cropName] = [
        'crop_name' => $cropName,
        'current_price' => $currentPrice,
        'trend' => $trendData['trend'],
        'trend_percentage' => $trendData['percentage'],
        'trend_direction' => $trendData['direction'],
        'demand_level' => $demandLevel,
        'demand_text' => $demandText,
        'recommendation' => $recommendation,
        'optimal_sell_window' => $sellWindow
    ];
}

// Store insights in database for caching
foreach ($insights as $cropName => $data) {
    $stmt = mysqli_prepare($conn, "
        INSERT INTO market_insights 
        (crop_name, current_price, price_trend, trend_percentage, recommendation, optimal_sell_window, demand_level, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
        current_price = VALUES(current_price),
        price_trend = VALUES(price_trend),
        trend_percentage = VALUES(trend_percentage),
        recommendation = VALUES(recommendation),
        optimal_sell_window = VALUES(optimal_sell_window),
        demand_level = VALUES(demand_level),
        created_at = NOW()
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "sdsdsss",
        $cropName,
        $data['current_price'],
        $data['trend'],
        $data['trend_percentage'],
        $data['recommendation'],
        $data['optimal_sell_window'],
        $data['demand_text']
    );

    mysqli_stmt_execute($stmt);
}

echo json_encode([
    'success' => true,
    'insights' => array_values($insights),
    'generated_at' => date('Y-m-d H:i:s')
]);
?>