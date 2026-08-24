<?php
include 'includes/db.php';
$tables = ['diseases', 'user_diseases', 'remedies', 'remedy_diseases', 'health_records', 'wellness_scores', 'user_remedies', 'appointments', 'medications', 'symptoms_log'];

foreach ($tables as $table) {
    $res = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($res) > 0) {
        echo "$table: EXISTS\n";
    } else {
        echo "$table: MISSING\n";
    }
}
?>
