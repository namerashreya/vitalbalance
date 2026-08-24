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

if ($medication_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid medication ID']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Delete medication
$query = "DELETE FROM medications WHERE medication_id = $medication_id AND user_id = $user_id";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        'success' => true,
        'message' => 'Medication deleted successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete medication'
    ]);
}

mysqli_close($conn);
?>
