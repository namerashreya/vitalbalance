<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check table structure
$columns_check = mysqli_query($conn, "SHOW COLUMNS FROM health_records");
$has_recorded_at = false;
$has_date_recorded = false;
$has_created_at = false;
$primary_key = null;
while ($col = mysqli_fetch_assoc($columns_check)) {
    if ($col['Field'] === 'recorded_at') $has_recorded_at = true;
    if ($col['Field'] === 'date_recorded') $has_date_recorded = true;
    if ($col['Field'] === 'created_at') $has_created_at = true;
    if ($col['Key'] === 'PRI') {
        $primary_key = $col['Field'];
    }
}

$date_field = $primary_key; // fallback to primary key
if ($has_recorded_at) {
    $date_field = 'recorded_at';
} elseif ($has_date_recorded) {
    $date_field = 'date_recorded';
} elseif ($has_created_at) {
    $date_field = 'created_at';
}

// Total records
$total_query = "SELECT COUNT(*) as count FROM health_records WHERE user_id = $user_id";
$total_result = mysqli_query($conn, $total_query);
$total = mysqli_fetch_assoc($total_result)['count'];

// This week
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_query = "SELECT COUNT(*) as count FROM health_records WHERE user_id = $user_id AND DATE($date_field) >= '$week_start'";
$week_result = mysqli_query($conn, $week_query);
$this_week = mysqli_fetch_assoc($week_result)['count'];

// Streak (consecutive days with records)
$streak = 0;
$streak_query = "SELECT DATE($date_field) as date FROM health_records WHERE user_id = $user_id GROUP BY DATE($date_field) ORDER BY date DESC LIMIT 30";
$streak_result = mysqli_query($conn, $streak_query);
$dates = [];
while ($row = mysqli_fetch_assoc($streak_result)) {
    $dates[] = $row['date'];
}
if (count($dates) > 0) {
    $today = date('Y-m-d');
    $check_date = $today;
    foreach ($dates as $date) {
        if ($date === $check_date) {
            $streak++;
            $check_date = date('Y-m-d', strtotime($check_date . ' -1 day'));
        } else {
            break;
        }
    }
}

// Improvement (compare last 7 days vs previous 7 days)
$improvement = 0;
$recent_query = "SELECT AVG(CAST(value AS DECIMAL(10,2))) as avg FROM health_records WHERE user_id = $user_id AND record_type = 'weight' AND DATE($date_field) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$recent_result = mysqli_query($conn, $recent_query);
$recent_avg = mysqli_fetch_assoc($recent_result)['avg'];

$previous_query = "SELECT AVG(CAST(value AS DECIMAL(10,2))) as avg FROM health_records WHERE user_id = $user_id AND record_type = 'weight' AND DATE($date_field) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND DATE($date_field) < DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$previous_result = mysqli_query($conn, $previous_query);
$previous_avg = mysqli_fetch_assoc($previous_result)['avg'];

if ($previous_avg > 0 && $recent_avg > 0) {
    $improvement = round((($previous_avg - $recent_avg) / $previous_avg) * 100);
}

echo json_encode([
    'success' => true,
    'stats' => [
        'total_records' => (int)$total,
        'this_week' => (int)$this_week,
        'streak' => (int)$streak,
        'improvement' => (int)$improvement
    ]
]);

mysqli_close($conn);
?>
