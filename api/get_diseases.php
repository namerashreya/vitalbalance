<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';

// Get all available diseases
$query = "SELECT * FROM diseases WHERE 1=1";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $query .= " AND (disease_name LIKE '%$search%' OR description LIKE '%$search%' OR category LIKE '%$search%')";
}

$query .= " ORDER BY disease_name ASC";

$result = mysqli_query($conn, $query);

$diseases = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Check if user already has this disease
    $user_has = mysqli_query($conn, "SELECT id FROM user_diseases WHERE user_id = $user_id AND disease_id = {$row['disease_id']}");
    $row['user_has'] = ($user_has && mysqli_num_rows($user_has) > 0);
    
    $diseases[] = $row;
}

echo json_encode([
    'success' => true,
    'diseases' => $diseases
]);

mysqli_close($conn);
?>

