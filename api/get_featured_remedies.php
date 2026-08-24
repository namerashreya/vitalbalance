<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

// Fetch 3 random approved remedies from the database
$query = "SELECT remedy_id, remedy_name, category, description, instructions, image 
          FROM remedies 
          WHERE approved_by_admin = 1 
          ORDER BY RAND() 
          LIMIT 3";

$result = mysqli_query($conn, $query);

if ($result) {
    $remedies = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $remedies[] = [
            'remedy_id' => $row['remedy_id'],
            'remedy_name' => $row['remedy_name'],
            'category' => $row['category'],
            'description' => $row['description'],
            'instructions' => $row['instructions'],
            'image' => $row['image']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'remedies' => $remedies,
        'count' => count($remedies)
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
