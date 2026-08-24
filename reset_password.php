<?php
session_start();
include("includes/db.php");

$error = "";
$success = "";
$valid_token = false;

// Check if token is provided
if(isset($_GET['token']) && !empty($_GET['token'])){
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    
    // Verify token and check if it's not expired
    $check_query = "SELECT * FROM users WHERE reset_token='$token' AND reset_expires > NOW()";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) > 0){
        $valid_token = true;
        $user = mysqli_fetch_assoc($check_result);
        
        // Process password reset
        if(isset($_POST['reset_password'])){
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            if(empty($password) || empty($confirm_password)){
                $error = "All fields are required";
            } elseif($password !== $confirm_password){
                $error = "Passwords do not match";
            } elseif(strlen($password) < 6){
                $error = "Password must be at least 6 characters long";
            } else {
                // Hash new password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Update password and clear reset token
                $update_query = "UPDATE users SET password='$hashed_password', reset_token=NULL, reset_expires=NULL WHERE reset_token='$token'";
                
                if(mysqli_query($conn, $update_query)){
                    $success = "Password reset successfully! You can now login with your new password.";
                    $valid_token = false; // Hide form after success
                } else {
                    $error = "Failed to reset password. Please try again.";
                }
            }
        }
    } else {
        $error = "Invalid or expired reset token. Please request a new password reset.";
    }
} else {
    $error = "No reset token provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Reset Password</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h1>VitalBalance</h1>
        <p class="tagline">Reset Your Password</p>

        <?php if(isset($error) && $error != "") { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <?php if(isset($success) && $success != "") { ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px;">
                <?php echo $success; ?>
            </div>
        <?php } ?>

        <?php if($valid_token) { ?>
            <form method="POST">
                <input type="password" name="password" placeholder="New Password (min. 6 characters)" required>
                
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>

                <button type="submit" name="reset_password">Reset Password</button>
            </form>
        <?php } ?>

        <div class="links">
            <a href="login.php">Back to Login</a> |
            <a href="forgot_password.php">Request New Reset Link</a>
        </div>
    </div>
</div>

</body>
</html>

