<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's remedies
$query = "SELECT * FROM user_remedies WHERE user_id = $user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if ($result) {
    $remedies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $remedies[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'remedies' => $remedies
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch remedies',
        'error' => mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
