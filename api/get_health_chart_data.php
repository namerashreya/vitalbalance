<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$type = $_GET['type'] ?? 'blood_pressure';
$days = (int)($_GET['days'] ?? 30);

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

$start_date = date('Y-m-d', strtotime("-$days days"));

$data = [
    'success' => true,
    'labels' => [],
    'values' => []
];

// Get records for the specified period
$type = mysqli_real_escape_string($conn, $type);
$query = "SELECT value, $date_field 
          FROM health_records 
          WHERE user_id = $user_id 
          AND record_type = '$type'";
          
if ($has_recorded_at || $has_created_at) {
    $query .= " AND DATE($date_field) >= '$start_date'";
}

$query .= " ORDER BY $date_field ASC LIMIT 30";
$result = mysqli_query($conn, $query);

$labels = [];
$values = [];

while ($record = mysqli_fetch_assoc($result)) {
    $date_value = $has_recorded_at ? $record['recorded_at'] : ($has_created_at ? $record['created_at'] : '');
    if ($date_value) {
        $date = date('M d', strtotime($date_value));
        $labels[] = $date;
        
        // Parse value
        $value_str = $record['value'];
        $value = 0;
        
        // Handle blood pressure (systolic/diastolic)
        if ($type == 'blood_pressure' && preg_match('/(\d+)\/(\d+)/', $value_str, $matches)) {
            $value = (int)$matches[1]; // Use systolic
        } else {
            // Extract numeric value
            preg_match('/(\d+\.?\d*)/', $value_str, $matches);
            if (isset($matches[1])) {
                $value = (float)$matches[1];
            }
        }
        
        $values[] = $value;
    }
}

$data['labels'] = $labels;
$data['values'] = $values;

echo json_encode($data);
mysqli_close($conn);
?>

