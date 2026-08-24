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
$recipe_id = intval($data['recipe_id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($recipe_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid recipe ID']);
    exit();
}

// Check if already favorited
$check_query = "SELECT * FROM user_favorites WHERE user_id = $user_id AND recipe_id = $recipe_id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    // Remove from favorites
    $query = "DELETE FROM user_favorites WHERE user_id = $user_id AND recipe_id = $recipe_id";
    $is_favorite = false;
} else {
    // Add to favorites
    $query = "INSERT INTO user_favorites (user_id, recipe_id) VALUES ($user_id, $recipe_id)";
    $is_favorite = true;
}

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'is_favorite' => $is_favorite]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update favorite: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>

