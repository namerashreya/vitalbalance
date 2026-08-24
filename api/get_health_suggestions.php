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

$suggestions = [];

// Get user's diseases
$diseases_query = "SELECT d.disease_name FROM user_diseases ud JOIN diseases d ON ud.disease_id = d.disease_id WHERE ud.user_id = $user_id";
$diseases_result = mysqli_query($conn, $diseases_query);
$user_diseases = [];
while ($row = mysqli_fetch_assoc($diseases_result)) {
    $user_diseases[] = $row['disease_name'];
}

// Check recent records
$recent_query = "SELECT record_type, value, $date_field FROM health_records WHERE user_id = $user_id ORDER BY $date_field DESC LIMIT 10";
$recent_result = mysqli_query($conn, $recent_query);
$recent_records = [];
while ($row = mysqli_fetch_assoc($recent_result)) {
    $recent_records[] = $row;
}

// Suggestion 1: Track more frequently
$last_record_query = "SELECT MAX($date_field) as last_date FROM health_records WHERE user_id = $user_id";
$last_record_result = mysqli_query($conn, $last_record_query);
$last_date = mysqli_fetch_assoc($last_record_result)['last_date'];
if ($last_date) {
    $days_since = (time() - strtotime($last_date)) / (60 * 60 * 24);
    if ($days_since > 2) {
        $suggestions[] = [
            'icon' => '📅',
            'title' => 'Time to Track!',
            'text' => 'It\'s been ' . round($days_since) . ' days since your last record. Regular tracking helps you see patterns and improvements.'
        ];
    }
}

// Suggestion 2: Blood pressure monitoring
if (in_array('Hypertension', $user_diseases) || in_array('Heart Disease', $user_diseases)) {
    $bp_count = 0;
    foreach ($recent_records as $record) {
        if ($record['record_type'] === 'blood_pressure') $bp_count++;
    }
    if ($bp_count < 3) {
        $suggestions[] = [
            'icon' => '❤️',
            'title' => 'Monitor Blood Pressure',
            'text' => 'For your condition, tracking blood pressure 2-3 times per week helps manage your health better.'
        ];
    }
}

// Suggestion 3: Blood sugar for diabetes
if (in_array('Diabetes Type 2', $user_diseases) || in_array('Diabetes Type 1', $user_diseases) || in_array('Prediabetes', $user_diseases)) {
    $sugar_count = 0;
    foreach ($recent_records as $record) {
        if ($record['record_type'] === 'blood_sugar') $sugar_count++;
    }
    if ($sugar_count < 5) {
        $suggestions[] = [
            'icon' => '🩸',
            'title' => 'Track Blood Sugar',
            'text' => 'Regular blood sugar monitoring is crucial for managing diabetes. Aim for daily tracking.'
        ];
    }
}

// Suggestion 4: Weight tracking
$weight_records = [];
foreach ($recent_records as $record) {
    if ($record['record_type'] === 'weight') {
        $weight_records[] = floatval($record['value']);
    }
}
if (count($weight_records) >= 2) {
    $trend = $weight_records[0] - $weight_records[count($weight_records) - 1];
    if (abs($trend) > 0) {
        $direction = $trend > 0 ? 'increased' : 'decreased';
        $suggestions[] = [
            'icon' => $trend < 0 ? '📉' : '📈',
            'title' => 'Weight Trend',
            'text' => 'Your weight has ' . $direction . ' by ' . round(abs($trend), 1) . ' units. Keep tracking to maintain your goals!'
        ];
    }
}

// Suggestion 5: General encouragement
if (count($recent_records) < 3) {
    $suggestions[] = [
        'icon' => '💡',
        'title' => 'Start Tracking',
        'text' => 'Add more records to get personalized insights and track your health journey effectively.'
    ];
} else {
    $suggestions[] = [
        'icon' => '🌟',
        'title' => 'Great Job!',
        'text' => 'You\'re doing well with tracking. Consider adding notes to your records for better insights.'
    ];
}

// If no specific suggestions, add general ones
if (empty($suggestions)) {
    $suggestions[] = [
        'icon' => '📊',
        'title' => 'Keep Tracking',
        'text' => 'Consistent tracking helps you understand your health patterns and make informed decisions.'
    ];
}

echo json_encode([
    'success' => true,
    'suggestions' => array_slice($suggestions, 0, 5) // Limit to 5 suggestions
]);

mysqli_close($conn);
?>
