<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all medications for the user
$query = "SELECT * FROM medications WHERE user_id = $user_id ORDER BY 
          CASE status 
            WHEN 'active' THEN 1 
            WHEN 'completed' THEN 2 
            WHEN 'stopped' THEN 3 
          END, created_at DESC";

$result = mysqli_query($conn, $query);
$medications = [];

while ($row = mysqli_fetch_assoc($result)) {
    $medications[] = $row;
}

echo json_encode([
    'success' => true,
    'medications' => $medications,
    'count' => count($medications)
]);

mysqli_close($conn);
?>
