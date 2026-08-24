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
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$remedy_name = mysqli_real_escape_string($conn, $_POST['remedy_name'] ?? '');
$remedy_notes = mysqli_real_escape_string($conn, $_POST['remedy_notes'] ?? '');

// Validation
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid remedy ID']);
    exit();
}

if (empty($remedy_name)) {
    echo json_encode(['success' => false, 'message' => 'Remedy name is required']);
    exit();
}

// Check if the remedy belongs to the user
$check_query = "SELECT id FROM user_remedies WHERE id = $id AND user_id = $user_id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Update user remedy
$query = "UPDATE user_remedies 
          SET remedy_name = '$remedy_name', 
              remedy_notes = " . ($remedy_notes ? "'$remedy_notes'" : "NULL") . "
          WHERE id = $id AND user_id = $user_id";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        'success' => true,
        'message' => 'Remedy updated successfully!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update remedy',
        'error' => mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
