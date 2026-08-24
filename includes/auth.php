<?php
// Authentication check - include this at the top of protected pages
if(!isset($_SESSION['user_id'])){
    // Check if we're in a subdirectory
    $redirect_path = (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) 
        ? '../index.php' 
        : 'index.php';
    header("Location: " . $redirect_path);
    exit();
}
?>

