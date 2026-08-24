<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$filter = $_GET['filter'] ?? 'all';
$date = $_GET['date'] ?? null;

// Check table structure
$columns_check = mysqli_query($conn, "SHOW COLUMNS FROM health_records");
$has_recorded_at = false;
$has_date_recorded = false;
$has_created_at = false;
$has_value = false;
$has_unit = false;
$has_metric_value = false;
$has_metric_unit = false;
$primary_key = null;

while ($col = mysqli_fetch_assoc($columns_check)) {
    if ($col['Field'] === 'recorded_at') $has_recorded_at = true;
    if ($col['Field'] === 'date_recorded') $has_date_recorded = true;
    if ($col['Field'] === 'created_at') $has_created_at = true;
    if ($col['Field'] === 'value') $has_value = true;
    if ($col['Field'] === 'unit') $has_unit = true;
    if ($col['Field'] === 'metric_value') $has_metric_value = true;
    if ($col['Field'] === 'metric_unit') $has_metric_unit = true;
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

// Build SELECT query with proper field mapping
$select_fields = ['*'];
$query = "SELECT * FROM health_records WHERE user_id = $user_id";

if ($filter !== 'all') {
    $filter = mysqli_real_escape_string($conn, $filter);
    $query .= " AND record_type = '$filter'";
}

if ($date) {
    $date = mysqli_real_escape_string($conn, $date);
    $query .= " AND DATE($date_field) = '$date'";
}

$query .= " ORDER BY $date_field DESC LIMIT 50";

$result = mysqli_query($conn, $query);
$records = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Normalize the record structure
    $record = [
        'record_id' => $row['record_id'] ?? $row[$primary_key],
        'record_type' => $row['record_type'],
        'value' => $row['value'] ?? $row['metric_value'] ?? '',
        'unit' => $row['unit'] ?? $row['metric_unit'] ?? '',
        'notes' => $row['notes'] ?? $row['description'] ?? '',
        'recorded_at' => $row['recorded_at'] ?? ($row['date_recorded'] . ' 00:00:00') ?? $row['created_at'],
    ];
    
    $records[] = $record;
}

echo json_encode([
    'success' => true,
    'records' => $records,
    'count' => count($records)
]);

mysqli_close($conn);
?>
