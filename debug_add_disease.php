<?php
// Debug script to test disease insertion
// Access this file directly in browser: http://localhost/vitalbalance_db/debug_add_disease.php

session_start();
include("includes/db.php");

echo "<h2>Debug: Add Disease Test</h2>";

// Check session
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color:red;'>Error: Not logged in. Please login first.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];
echo "<p>User ID: $user_id</p>";

// Check diseases table
echo "<h3>1. Checking diseases table:</h3>";
$diseases_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM diseases");
if ($diseases_check) {
    $row = mysqli_fetch_assoc($diseases_check);
    echo "<p>Total diseases in database: " . $row['count'] . "</p>";
} else {
    echo "<p style='color:red;'>Error checking diseases: " . mysqli_error($conn) . "</p>";
}

// Check user_diseases table structure
echo "<h3>2. Checking user_diseases table structure:</h3>";
$structure = mysqli_query($conn, "DESCRIBE user_diseases");
if ($structure) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($col = mysqli_fetch_assoc($structure)) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
}

// Test insert query
echo "<h3>3. Test Insert Query (dry run):</h3>";
$test_disease_id = 1; // Assuming disease_id 1 exists
$test_status = 'active';
$test_date = '2024-01-01';
$test_notes = 'Test notes';

// Check what columns exist
$columns = mysqli_query($conn, "SHOW COLUMNS FROM user_diseases");
$column_names = [];
while ($col = mysqli_fetch_assoc($columns)) {
    $column_names[] = $col['Field'];
}

echo "<p>Available columns: " . implode(', ', $column_names) . "</p>";

// Build query based on available columns
$insert_cols = ['user_id', 'disease_id'];
$insert_vals = [$user_id, $test_disease_id];

if (in_array('diagnosed_date', $column_names)) {
    $insert_cols[] = 'diagnosed_date';
    $insert_vals[] = "'$test_date'";
}
if (in_array('status', $column_names)) {
    $insert_cols[] = 'status';
    $insert_vals[] = "'$test_status'";
}
if (in_array('notes', $column_names)) {
    $insert_cols[] = 'notes';
    $insert_vals[] = "'$test_notes'";
}

$test_query = "INSERT INTO user_diseases (" . implode(', ', $insert_cols) . ") VALUES (" . implode(', ', $insert_vals) . ")";
echo "<p><strong>Test Query:</strong></p>";
echo "<pre style='background:#f0f0f0; padding:10px;'>" . htmlspecialchars($test_query) . "</pre>";

// Check existing user diseases
echo "<h3>4. Your current diseases:</h3>";
$user_diseases = mysqli_query($conn, "SELECT ud.*, d.disease_name FROM user_diseases ud 
                                      JOIN diseases d ON ud.disease_id = d.disease_id 
                                      WHERE ud.user_id = $user_id");
if ($user_diseases && mysqli_num_rows($user_diseases) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Disease Name</th><th>Status</th><th>Diagnosed Date</th></tr>";
    while ($ud = mysqli_fetch_assoc($user_diseases)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($ud['disease_name']) . "</td>";
        echo "<td>" . htmlspecialchars($ud['status'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($ud['diagnosed_date'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No diseases added yet.</p>";
}

mysqli_close($conn);
?>
