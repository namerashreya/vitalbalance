<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user_disease_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$user_disease_id = intval($data['user_disease_id']);
$status = mysqli_real_escape_string($conn, $data['status']);

// Validate status
$allowed_statuses = ['active', 'managed', 'recovered'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Update the status ensuring the disease belongs to the user
$query = "UPDATE user_diseases 
          SET status = '$status' 
          WHERE id = $user_disease_id AND user_id = $user_id";

if (isset($data['notes'])) {
    $notes = mysqli_real_escape_string($conn, $data['notes']);
    $query = "UPDATE user_diseases 
              SET status = '$status', notes = '$notes'
              WHERE id = $user_disease_id AND user_id = $user_id";
}

if (mysqli_query($conn, $query)) {
    if (mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        // Query logic was correct but no rows updated - likely ID mismatch or no change
        // Check if record exists
        $check = mysqli_query($conn, "SELECT id FROM user_diseases WHERE id = $user_disease_id AND user_id = $user_id");
        if (mysqli_num_rows($check) > 0) {
             echo json_encode(['success' => true, 'message' => 'No changes made']);
        } else {
             echo json_encode(['success' => false, 'message' => 'Record not found']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}
?>
