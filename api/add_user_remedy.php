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
$remedy_name = mysqli_real_escape_string($conn, $_POST['remedy_name'] ?? '');
$remedy_notes = mysqli_real_escape_string($conn, $_POST['remedy_notes'] ?? '');

// Validation
if (empty($remedy_name)) {
    echo json_encode(['success' => false, 'message' => 'Remedy name is required']);
    exit();
}

// Insert user remedy
$query = "INSERT INTO user_remedies (user_id, remedy_name, remedy_notes, created_at) 
          VALUES ($user_id, '$remedy_name', " . 
          ($remedy_notes ? "'$remedy_notes'" : "NULL") . ", NOW())";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        'success' => true,
        'message' => 'Remedy added successfully!',
        'id' => mysqli_insert_id($conn)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add remedy',
        'error' => mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
