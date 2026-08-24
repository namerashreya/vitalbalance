<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$record_type = mysqli_real_escape_string($conn, $_POST['record_type'] ?? '');
$value = mysqli_real_escape_string($conn, $_POST['value'] ?? '');
$unit = mysqli_real_escape_string($conn, $_POST['unit'] ?? '');
$recorded_at_raw = $_POST['recorded_at'] ?? date('Y-m-d H:i:s');
$notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');

// Validation
if (empty($record_type) || empty($value)) {
    echo json_encode(['success' => false, 'message' => 'Record type and value are required']);
    exit();
}

// Check what columns exist in the table
$columns_result = mysqli_query($conn, "SHOW COLUMNS FROM health_records");
$existing_columns = [];
while ($col = mysqli_fetch_assoc($columns_result)) {
    $existing_columns[] = $col['Field'];
}

// Prepare the INSERT query based on existing columns
$insert_cols = ['user_id'];
$insert_vals = ["$user_id"];

// Handle record_type column
if (in_array('record_type', $existing_columns)) {
    $insert_cols[] = 'record_type';
    $insert_vals[] = "'$record_type'";
}

// Handle title column (fallback if record_type doesn't exist)
if (in_array('title', $existing_columns)) {
    $title = ucwords(str_replace('_', ' ', $record_type));
    $insert_cols[] = 'title';
    $insert_vals[] = "'$title'";
}

// Handle value columns
if (in_array('value', $existing_columns)) {
    $insert_cols[] = 'value';
    $insert_vals[] = "'$value'";
}
if (in_array('metric_value', $existing_columns)) {
    $insert_cols[] = 'metric_value';
    $insert_vals[] = "'$value'";
}

// Handle unit columns
if (in_array('unit', $existing_columns)) {
    $insert_cols[] = 'unit';
    $insert_vals[] = $unit ? "'$unit'" : "NULL";
}
if (in_array('metric_unit', $existing_columns)) {
    $insert_cols[] = 'metric_unit';
    $insert_vals[] = $unit ? "'$unit'" : "NULL";
}

// Handle date columns
$recorded_at = mysqli_real_escape_string($conn, $recorded_at_raw);
$date_only = date('Y-m-d', strtotime($recorded_at));

if (in_array('recorded_at', $existing_columns)) {
    $insert_cols[] = 'recorded_at';
    $insert_vals[] = "'$recorded_at'";
}
if (in_array('date_recorded', $existing_columns)) {
    $insert_cols[] = 'date_recorded';
    $insert_vals[] = "'$date_only'";
}

// Handle notes/description columns
if (in_array('notes', $existing_columns) && $notes) {
    $insert_cols[] = 'notes';
    $insert_vals[] = "'$notes'";
}
if (in_array('description', $existing_columns) && $notes) {
    $insert_cols[] = 'description';
    $insert_vals[] = "'$notes'";
}

// Build and execute the INSERT query
$query = "INSERT INTO health_records (" . implode(', ', $insert_cols) . ") 
          VALUES (" . implode(', ', $insert_vals) . ")";

// Execute the query
if (mysqli_query($conn, $query)) {
    $record_id = mysqli_insert_id($conn);
    echo json_encode([
        'success' => true, 
        'message' => 'Record added successfully! 🎉',
        'record_id' => $record_id,
        'data' => [
            'type' => $record_type,
            'value' => $value,
            'unit' => $unit,
            'date' => $recorded_at
        ]
    ]);
} else {
    $error = mysqli_error($conn);
    error_log("Health record insert error: " . $error);
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to save record. Please try again.',
        'error' => $error,
        'query' => $query // For debugging - remove in production
    ]);
}

mysqli_close($conn);
?>
