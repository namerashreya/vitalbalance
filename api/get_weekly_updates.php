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

$updates = [];

// Get user's diseases
$diseases_query = "SELECT ud.*, d.disease_name FROM user_diseases ud JOIN diseases d ON ud.disease_id = d.disease_id WHERE ud.user_id = $user_id";
$diseases_result = mysqli_query($conn, $diseases_query);
$user_diseases = [];
while ($row = mysqli_fetch_assoc($diseases_result)) {
    $user_diseases[] = $row;
}

// Get records from this week
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_query = "SELECT record_type, COUNT(*) as count FROM health_records WHERE user_id = $user_id AND DATE($date_field) >= '$week_start' GROUP BY record_type";
$week_result = mysqli_query($conn, $week_query);
$week_records = [];
while ($row = mysqli_fetch_assoc($week_result)) {
    $week_records[$row['record_type']] = $row['count'];
}

// Weekly summary
$total_week = array_sum($week_records);
if ($total_week > 0) {
    $updates[] = [
        'date' => 'This Week',
        'text' => "You've recorded $total_week health entries this week. " . ($total_week >= 7 ? 'Excellent consistency! 🌟' : 'Keep it up! 💪')
    ];
}

// Disease-specific updates
foreach ($user_diseases as $disease) {
    $disease_name = $disease['disease_name'];
    $status = isset($disease['status']) ? $disease['status'] : 'active';
    
    // Check for relevant records
    $relevant_types = [];
    if (stripos($disease_name, 'diabetes') !== false || stripos($disease_name, 'blood sugar') !== false) {
        $relevant_types[] = 'blood_sugar';
    }
    if (stripos($disease_name, 'hypertension') !== false || stripos($disease_name, 'heart') !== false || stripos($disease_name, 'blood pressure') !== false) {
        $relevant_types[] = 'blood_pressure';
        $relevant_types[] = 'heart_rate';
    }
    
    $relevant_count = 0;
    foreach ($relevant_types as $type) {
        if (isset($week_records[$type])) {
            $relevant_count += $week_records[$type];
        }
    }
    
    if ($relevant_count > 0) {
        $updates[] = [
            'date' => date('M d'),
            'text' => "$disease_name: Tracked $relevant_count relevant metrics this week. " . ($status === 'managed' ? 'Great management! ✅' : 'Keep monitoring! 📊')
        ];
    } else {
        $updates[] = [
            'date' => date('M d'),
            'text' => "$disease_name: Consider tracking relevant metrics this week for better management."
        ];
    }
}

// Reminders for upcoming week
$updates[] = [
    'date' => 'Next Week',
    'text' => 'Set a goal to track at least 5 health metrics next week for better insights.'
];

// If no updates, add encouragement
if (empty($updates)) {
    $updates[] = [
        'date' => 'This Week',
        'text' => 'Start tracking your health metrics to see weekly updates and progress!'
    ];
}

echo json_encode([
    'success' => true,
    'updates' => array_slice($updates, 0, 5) // Limit to 5 updates
]);

mysqli_close($conn);
?>
