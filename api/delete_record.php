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

$data = json_decode(file_get_contents('php://input'), true);
$record_id = intval($data['record_id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($record_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid record ID']);
    exit();
}

// Verify ownership before deleting
$check_query = "SELECT * FROM health_records WHERE record_id = $record_id AND user_id = $user_id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Record not found or unauthorized']);
    exit();
}

// Delete record
$query = "DELETE FROM health_records WHERE record_id = $record_id AND user_id = $user_id";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Record deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete record: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>

