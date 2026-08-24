<?php
session_start();
include("includes/db.php");

$error = "";
$success = "";

if(isset($_POST['reset_password'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if(empty($email) || empty($password) || empty($confirm_password)){
        $error = "All fields are required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Invalid email format";
    } elseif($password !== $confirm_password){
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6){
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if email exists
        $check_query = "SELECT * FROM users WHERE email='$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if(mysqli_num_rows($check_result) == 0){
            // Don't reveal if email exists for security
            $error = "Invalid email or password reset failed.";
        } else {
            // Hash new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Update password directly
            $update_query = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
            
            if(mysqli_query($conn, $update_query)){
                $success = "Password reset successfully! You can now login with your new password.";
            } else {
                $error = "Failed to reset password. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/design-system-v2.css">
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
            <div class="success-msg">
                <?php echo $success; ?>
            </div>
        <?php } ?>

        <form method="POST">
            <p style="margin-bottom: 15px; color: #666; font-size: 14px;">
                Enter your email address and new password to reset your password.
            </p>
            
            <input type="email" name="email" placeholder="Email Address" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            <input type="password" name="password" placeholder="New Password (min. 6 characters)" required>
            
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>

            <button type="submit" name="reset_password">Reset Password</button>
        </form>

        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>

