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
$data = json_decode(file_get_contents('php://input'), true);
$user_disease_id = (int)($data['user_disease_id'] ?? 0);

if ($user_disease_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid disease ID']);
    exit();
}

// Check what the primary key column is called
$pk_check = mysqli_query($conn, "SHOW COLUMNS FROM user_diseases");
$primary_key = 'id'; // default
while ($col = mysqli_fetch_assoc($pk_check)) {
    if ($col['Key'] === 'PRI') {
        $primary_key = $col['Field'];
        break;
    }
}

// Verify that this disease belongs to the user
$verify_query = "SELECT $primary_key FROM user_diseases WHERE $primary_key = $user_disease_id AND user_id = $user_id";
$verify_result = mysqli_query($conn, $verify_query);

if (!$verify_result || mysqli_num_rows($verify_result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Disease not found or access denied']);
    exit();
}

// Delete the user disease
$delete_query = "DELETE FROM user_diseases WHERE $primary_key = $user_disease_id AND user_id = $user_id";

if (mysqli_query($conn, $delete_query)) {
    echo json_encode(['success' => true, 'message' => 'Disease removed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove disease: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>

