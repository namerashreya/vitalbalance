<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$filter_type = $_GET['filter_type'] ?? 'all';
$filter_value = $_GET['filter_value'] ?? '';

$data = [
    'success' => true,
    'stats' => [],
    'records' => [],
    'diseases' => [],
    'recipes' => []
];

// Get filtered health records
$records_query = "SELECT * FROM health_records WHERE user_id = $user_id";
if ($filter_type == 'record_type' && $filter_value) {
    $filter_value = mysqli_real_escape_string($conn, $filter_value);
    $records_query .= " AND record_type = '$filter_value'";
}
$records_query .= " ORDER BY recorded_at DESC LIMIT 10";
$records_result = mysqli_query($conn, $records_query);
while ($record = mysqli_fetch_assoc($records_result)) {
    $data['records'][] = $record;
}

// Get user's diseases
$diseases_query = "SELECT ud.*, d.disease_name, d.description, d.icon, d.color_code 
                   FROM user_diseases ud 
                   JOIN diseases d ON ud.disease_id = d.disease_id 
                   WHERE ud.user_id = $user_id";
if ($filter_type == 'disease' && $filter_value) {
    $filter_value = (int)$filter_value;
    $diseases_query .= " AND ud.disease_id = $filter_value";
}
$diseases_query .= " ORDER BY ud.created_at DESC";
$diseases_result = mysqli_query($conn, $diseases_query);
while ($disease = mysqli_fetch_assoc($diseases_result)) {
    $data['diseases'][] = $disease;
}

// Get related recipes based on user's diseases
if ($filter_type == 'disease' && $filter_value) {
    // Get recipes that might help with this disease (based on tags/description)
    $recipes_query = "SELECT * FROM recipes WHERE 1=1";
    // Add disease-specific filtering logic here
    $recipes_query .= " ORDER BY created_at DESC LIMIT 6";
} else {
    // Get all recipes or recipes related to user's diseases
    $user_disease_ids = [];
    $user_diseases_result = mysqli_query($conn, "SELECT disease_id FROM user_diseases WHERE user_id = $user_id");
    while ($ud = mysqli_fetch_assoc($user_diseases_result)) {
        $user_disease_ids[] = $ud['disease_id'];
    }
    
    $recipes_query = "SELECT * FROM recipes ORDER BY created_at DESC LIMIT 6";
}
$recipes_result = mysqli_query($conn, $recipes_query);
while ($recipe = mysqli_fetch_assoc($recipes_result)) {
    $data['recipes'][] = $recipe;
}

// Get statistics
$data['stats'] = [
    'disease_count' => mysqli_num_rows(mysqli_query($conn, "SELECT * FROM user_diseases WHERE user_id = $user_id")),
    'records_count' => mysqli_num_rows(mysqli_query($conn, "SELECT * FROM health_records WHERE user_id = $user_id")),
    'recipes_count' => mysqli_num_rows(mysqli_query($conn, "SELECT * FROM recipes"))
];

echo json_encode($data);
mysqli_close($conn);
?>

