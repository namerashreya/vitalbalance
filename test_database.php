<?php
// Test script to verify database setup and health records functionality
session_start();
include("includes/db.php");

echo "<!DOCTYPE html>
<html>
<head>
    <title>VitalHealth - Database Test</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .test-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 30px;
            margin: 20px 0;
            color: #333;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        h2 {
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .success {
            color: #10b981;
            font-weight: bold;
        }
        .error {
            color: #ef4444;
            font-weight: bold;
        }
        .info {
            background: #eff6ff;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #3b82f6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f3f4f6;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px 5px;
            font-weight: 600;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <h1>🏥 VitalHealth Database Test</h1>";

// Test 1: Database Connection
echo "<div class='test-card'>
    <h2>1. Database Connection</h2>";
if ($conn) {
    echo "<p class='success'>✓ Database connected successfully!</p>";
    echo "<p>Database: " . mysqli_get_host_info($conn) . "</p>";
} else {
    echo "<p class='error'>✗ Database connection failed!</p>";
    echo "<p>Error: " . mysqli_connect_error() . "</p>";
}
echo "</div>";

// Test 2: Check if health_records table exists
echo "<div class='test-card'>
    <h2>2. Health Records Table</h2>";
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'health_records'");
if (mysqli_num_rows($table_check) > 0) {
    echo "<p class='success'>✓ health_records table exists</p>";
    
    // Check table structure
    echo "<h3>Table Structure:</h3>";
    $columns = mysqli_query($conn, "SHOW COLUMNS FROM health_records");
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
    while ($col = mysqli_fetch_assoc($columns)) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . ($col['Key'] ?: '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>✗ health_records table does not exist!</p>";
    echo "<div class='info'>You need to create the health_records table. Run the database_schema.sql file.</div>";
}
echo "</div>";

// Test 3: Check for records
echo "<div class='test-card'>
    <h2>3. Existing Records</h2>";
$count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM health_records");
$count = mysqli_fetch_assoc($count_query)['count'];
echo "<p>Total records in database: <strong>$count</strong></p>";

if ($count > 0) {
    echo "<h3>Sample Records:</h3>";
    $sample = mysqli_query($conn, "SELECT * FROM health_records ORDER BY record_id DESC LIMIT 5");
    echo "<table>";
    echo "<tr><th>ID</th><th>Type</th><th>Value</th><th>Date</th></tr>";
    while ($row = mysqli_fetch_assoc($sample)) {
        echo "<tr>";
        echo "<td>" . ($row['record_id'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['record_type'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['value'] ?? $row['metric_value'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['recorded_at'] ?? $row['date_recorded'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='info'>No records found. Add some records through the Health Records page!</div>";
}
echo "</div>";

// Test 4: Check required columns
echo "<div class='test-card'>
    <h2>4. Column Compatibility Check</h2>";
$columns_check = mysqli_query($conn, "SHOW COLUMNS FROM health_records");
$columns = [];
while ($col = mysqli_fetch_assoc($columns_check)) {
    $columns[] = $col['Field'];
}

$required = [
    'user_id' => in_array('user_id', $columns),
    'record_type' => in_array('record_type', $columns),
    'value OR metric_value' => in_array('value', $columns) || in_array('metric_value', $columns),
    'recorded_at OR date_recorded' => in_array('recorded_at', $columns) || in_array('date_recorded', $columns),
];

echo "<table>";
echo "<tr><th>Required Field</th><th>Status</th></tr>";
foreach ($required as $field => $exists) {
    echo "<tr>";
    echo "<td>$field</td>";
    echo "<td>" . ($exists ? "<span class='success'>✓ Present</span>" : "<span class='error'>✗ Missing</span>") . "</td>";
    echo "</tr>";
}
echo "</table>";

$all_ok = !in_array(false, $required);
if ($all_ok) {
    echo "<p class='success'>✓ All required columns are present!</p>";
} else {
    echo "<p class='error'>✗ Some required columns are missing. Run the update_health_records_table.sql script.</p>";
}
echo "</div>";

// Test 5: API Endpoints
echo "<div class='test-card'>
    <h2>5. API Endpoints</h2>";
$apis = [
    'add_record.php' => 'Add Health Record',
    'get_health_records.php' => 'Get Health Records',
    'get_health_stats.php' => 'Get Health Stats',
    'get_health_chart_data.php' => 'Get Chart Data',
];

echo "<table>";
echo "<tr><th>API File</th><th>Purpose</th><th>Status</th></tr>";
foreach ($apis as $file => $purpose) {
    $path = "api/$file";
    $exists = file_exists($path);
    echo "<tr>";
    echo "<td>$file</td>";
    echo "<td>$purpose</td>";
    echo "<td>" . ($exists ? "<span class='success'>✓ Found</span>" : "<span class='error'>✗ Missing</span>") . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// Next Steps
echo "<div class='test-card'>
    <h2>📋 Next Steps</h2>
    <div class='info'>
        <ol>
            <li>If all tests passed, you're ready to use the Health Records feature!</li>
            <li>If any columns are missing, run the <code>update_health_records_table.sql</code> script in phpMyAdmin</li>
            <li>Login to your account and navigate to Health Records</li>
            <li>Try adding a new health record</li>
            <li>Check if the stats update correctly</li>
        </ol>
    </div>
    <div style='text-align: center; margin-top: 20px;'>
        <a href='index.php' class='btn'>Go to Homepage</a>
        <a href='user/health_records.php' class='btn'>Health Records</a>
    </div>
</div>";

echo "</body></html>";

mysqli_close($conn);
?>
