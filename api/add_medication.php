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
$medication_name = mysqli_real_escape_string($conn, $_POST['medication_name'] ?? '');
$dosage = mysqli_real_escape_string($conn, $_POST['dosage'] ?? '');
$frequency = mysqli_real_escape_string($conn, $_POST['frequency'] ?? '');
$start_date = mysqli_real_escape_string($conn, $_POST['start_date'] ?? date('Y-m-d'));
$end_date = mysqli_real_escape_string($conn, $_POST['end_date'] ?? '');
$prescribed_by = mysqli_real_escape_string($conn, $_POST['prescribed_by'] ?? '');
$natural_remedies = mysqli_real_escape_string($conn, $_POST['natural_remedies'] ?? '');
$notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');

// Validation
if (empty($medication_name) || empty($dosage) || empty($frequency)) {
    echo json_encode(['success' => false, 'message' => 'Medication name, dosage, and frequency are required']);
    exit();
}

// Insert medication
$query = "INSERT INTO medications (user_id, medication_name, dosage, frequency, start_date, end_date, prescribed_by, natural_remedies, notes, status) 
          VALUES ($user_id, '$medication_name', '$dosage', '$frequency', '$start_date', " . 
          ($end_date ? "'$end_date'" : "NULL") . ", " .
          ($prescribed_by ? "'$prescribed_by'" : "NULL") . ", " .
          ($natural_remedies ? "'$natural_remedies'" : "NULL") . ", " .
          ($notes ? "'$notes'" : "NULL") . ", 'active')";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        'success' => true,
        'message' => 'Medication added successfully!',
        'medication_id' => mysqli_insert_id($conn)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add medication',
        'error' => mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
