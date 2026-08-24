<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$medication_id = (int)($_GET['id'] ?? 0);

if ($medication_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid medication ID']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get medication details
$query = "SELECT * FROM medications WHERE medication_id = $medication_id AND user_id = $user_id";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'success' => true,
        'medication' => $row
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Medication not found'
    ]);
}

mysqli_close($conn);
?>
