<?php
include("includes/db.php");
$result = mysqli_query($conn, "SHOW COLUMNS FROM user_diseases");
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
