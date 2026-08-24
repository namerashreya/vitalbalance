<?php
// Test file to check if the API endpoint is accessible
// Access: http://localhost/vitalbalance_db/test_add_disease_api.php

echo "<h2>Testing Add Disease API</h2>";

// Test 1: Check if file exists
echo "<h3>1. File Check:</h3>";
if (file_exists('api/add_disease.php')) {
    echo "<p style='color:green;'>✓ api/add_disease.php exists</p>";
} else {
    echo "<p style='color:red;'>✗ api/add_disease.php not found</p>";
    exit();
}

// Test 2: Check database connection
echo "<h3>2. Database Connection:</h3>";
include("includes/db.php");
if ($conn) {
    echo "<p style='color:green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color:red;'>✗ Database connection failed</p>";
    exit();
}

// Test 3: Check if tables exist
echo "<h3>3. Table Check:</h3>";
$tables = ['diseases', 'user_diseases'];
foreach ($tables as $table) {
    $check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if ($check && mysqli_num_rows($check) > 0) {
        echo "<p style='color:green;'>✓ Table '$table' exists</p>";
        
        // Show table structure
        $structure = mysqli_query($conn, "DESCRIBE $table");
        echo "<details><summary>Show structure</summary><pre>";
        while ($col = mysqli_fetch_assoc($structure)) {
            echo $col['Field'] . " - " . $col['Type'] . "\n";
        }
        echo "</pre></details>";
    } else {
        echo "<p style='color:red;'>✗ Table '$table' does not exist</p>";
    }
}

// Test 4: Check if diseases exist
echo "<h3>4. Diseases Count:</h3>";
$count = mysqli_query($conn, "SELECT COUNT(*) as count FROM diseases");
if ($count) {
    $row = mysqli_fetch_assoc($count);
    echo "<p>Total diseases: " . $row['count'] . "</p>";
}

// Test 5: Check PHP syntax
echo "<h3>5. PHP Syntax Check:</h3>";
$output = [];
$return_var = 0;
exec('php -l api/add_disease.php 2>&1', $output, $return_var);
if ($return_var === 0) {
    echo "<p style='color:green;'>✓ PHP syntax is valid</p>";
} else {
    echo "<p style='color:red;'>✗ PHP syntax errors found:</p>";
    echo "<pre>" . implode("\n", $output) . "</pre>";
}

// Test 6: Check session
echo "<h3>6. Session Test:</h3>";
session_start();
if (isset($_SESSION['user_id'])) {
    echo "<p style='color:green;'>✓ Session active - User ID: " . $_SESSION['user_id'] . "</p>";
} else {
    echo "<p style='color:orange;'>⚠ No active session - You need to login first</p>";
}

mysqli_close($conn);
?>
