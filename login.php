<?php
session_start();
include("includes/db.php");

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if($user && password_verify($password, $user['password'])){
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] == 'admin'){
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/design-system-v2.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <div class="login-logo">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Rounded Gold Background -->
                <rect width="100" height="100" rx="25" fill="#C5A065"/>
                
                <!-- Plant Stem -->
                <path d="M50 82C50 82 50 60 52 25" stroke="#4a6b38" stroke-width="2" stroke-linecap="round"/>
                
                <!-- Leaves (Bright Green) -->
                <path d="M52 70C35 65 30 55 40 50C48 46 52 60 52 70Z" fill="#8BC34A"/>
                <path d="M52 60C69 55 74 45 64 40C56 36 52 50 52 60Z" fill="#8BC34A"/>
                <path d="M52 50C38 45 34 35 42 30C48 27 52 40 52 50Z" fill="#8BC34A"/>
                <path d="M52 40C66 35 70 25 62 20C56 18 52 30 52 40Z" fill="#8BC34A"/>
                <path d="M52 25C45 15 50 10 55 15C58 18 55 25 52 25Z" fill="#8BC34A"/>
            </svg>
        </div>
        <h1>VitalBalance</h1>
        <p class="tagline">Personalized Wellness & Chronic Care</p>

        <?php if(isset($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" name="login">Login</button>
        </form>

        <div class="links">
            <a href="register.php">Create Account</a> |
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</div>

</body>
</html>
