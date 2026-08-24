<?php
$conn = mysqli_connect('localhost', 'root', '', 'vitalbalance_db');
if (!$conn) die('Connection failed');

$updates = [
    'Olive Oil & Lemon' => 'https://images.unsplash.com/photo-1471943311424-64696066bc1b?auto=format&fit=crop&q=80&w=800',
    // Custom Deep Breathing photo stored locally; make sure the file exists at:
    // C:\xampp\htdocs\vitalbalance_db\assets\images\remedies\deep_breathing.jpg
    'Deep Breathing' => '../assets/images/remedies/deep_breathing.jpg'
];

foreach ($updates as $name => $url) {
    $safe_name = mysqli_real_escape_string($conn, $name);
    $q = "UPDATE remedies SET image = '$url' WHERE remedy_name = '$safe_name'";
    if (mysqli_query($conn, $q)) {
        echo "Updated $name to $url\n";
    } else {
        echo "Failed to update $name: " . mysqli_error($conn) . "\n";
    }
}
?>
