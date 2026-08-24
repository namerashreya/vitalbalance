<?php
$conn = mysqli_connect("localhost", "root", "", "vitalbalance_db");

if(!$conn){
    // Don't use die() as it outputs HTML and breaks JSON responses
    // Let the calling script handle the error
    $conn = false;
}
?>
