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

// Get recent activity
$recent_query = "SELECT COUNT(*) as count FROM health_records WHERE user_id = $user_id AND DATE($date_field) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$recent_result = mysqli_query($conn, $recent_query);
$recent_count = mysqli_fetch_assoc($recent_result)['count'];

// Get streak
$streak_query = "SELECT DATE($date_field) as date FROM health_records WHERE user_id = $user_id GROUP BY DATE($date_field) ORDER BY date DESC LIMIT 30";
$streak_result = mysqli_query($conn, $streak_query);
$dates = [];
while ($row = mysqli_fetch_assoc($streak_result)) {
    $dates[] = $row['date'];
}
$streak = 0;
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

// Motivational messages based on activity
$messages = [
    ['message' => 'You\'re doing amazing! 🎉', 'subtext' => 'Keep tracking your health daily'],
    ['message' => 'Consistency is key! 💪', 'subtext' => 'You\'re building great habits'],
    ['message' => 'Every record matters! 📊', 'subtext' => 'You\'re taking control of your health'],
    ['message' => 'Progress, not perfection! 🌟', 'subtext' => 'Keep up the excellent work'],
    ['message' => 'You\'re on the right track! 🚀', 'subtext' => 'Your dedication is inspiring']
];

$selected_message = $messages[array_rand($messages)];

if ($recent_count >= 7) {
    $selected_message = ['message' => 'Perfect week! 🌟', 'subtext' => 'You tracked every day this week!'];
} elseif ($streak >= 7) {
    $selected_message = ['message' => 'Amazing streak! 🔥', 'subtext' => $streak . ' days in a row!'];
} elseif ($recent_count >= 3) {
    $selected_message = ['message' => 'Great progress! 💪', 'subtext' => 'Keep it up!'];
}

// Achievements
$achievements = [];
if ($streak >= 7) {
    $achievements[] = ['icon' => '🔥', 'text' => '7 Day Streak'];
}
if ($recent_count >= 7) {
    $achievements[] = ['icon' => '⭐', 'text' => 'Weekly Champion'];
}

$total_query = "SELECT COUNT(*) as count FROM health_records WHERE user_id = $user_id";
$total_result = mysqli_query($conn, $total_query);
$total = mysqli_fetch_assoc($total_result)['count'];

if ($total >= 50) {
    $achievements[] = ['icon' => '🏆', 'text' => '50 Records'];
} elseif ($total >= 25) {
    $achievements[] = ['icon' => '🎖️', 'text' => '25 Records'];
} elseif ($total >= 10) {
    $achievements[] = ['icon' => '🎯', 'text' => '10 Records'];
}

echo json_encode([
    'success' => true,
    'message' => $selected_message['message'],
    'subtext' => $selected_message['subtext'],
    'achievements' => $achievements
]);

mysqli_close($conn);
?>
