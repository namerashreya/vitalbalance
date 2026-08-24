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
$remedy_id = intval($data['id'] ?? 0);

if ($remedy_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid remedy ID']);
    exit();
}

// Delete remedy (ensure it belongs to the user)
$query = "DELETE FROM user_remedies WHERE id = $remedy_id AND user_id = $user_id";

if (mysqli_query($conn, $query)) {
    if (mysqli_affected_rows($conn) > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Remedy deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Remedy not found or already deleted'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete remedy',
        'error' => mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
