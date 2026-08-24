<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$medication_id = (int)($data['medication_id'] ?? 0);
$status = mysqli_real_escape_string($conn, $data['status'] ?? 'completed');

if ($medication_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid medication ID']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Update medication status
$query = "UPDATE medications SET status = '$status' WHERE medication_id = $medication_id AND user_id = $user_id";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        'success' => true,
        'message' => 'Medication status updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update medication status'
    ]);
}

mysqli_close($conn);
?>
