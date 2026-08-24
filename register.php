<?php
session_start();
include("includes/db.php");

$error = "";
$success = "";

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $role = isset($_POST['role']) ? mysqli_real_escape_string($conn, $_POST['role']) : 'user'; // Default role

    // Validation
    if(empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($age) || empty($gender)){
        $error = "All fields are required";
    } elseif($password !== $confirm_password){
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6){
        $error = "Password must be at least 6 characters long";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Invalid email format";
    } elseif(!is_numeric($age) || $age < 1 || $age > 150){
        $error = "Please enter a valid age (1-150)";
    } elseif(!in_array($gender, ['Male', 'Female', 'Other'])){
        $error = "Please select a valid gender";
    } else {
        // Check if email already exists
        $check_query = "SELECT * FROM users WHERE email='$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if(mysqli_num_rows($check_result) > 0){
            $error = "Email already exists. Please use a different email.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user with all fields
            $insert_query = "INSERT INTO users (name, email, password, age, gender, role, created_at) 
                            VALUES ('$name', '$email', '$hashed_password', '$age', '$gender', '$role', NOW())";
            
            if(mysqli_query($conn, $insert_query)){
                $success = "Account created successfully! You can now login.";
            } else {
                // Try with alternative column names if needed
                $db_error = mysqli_error($conn);
                // Try with 'fullname' instead of 'name'
                if(strpos($db_error, "Unknown column 'name'") !== false){
                    $insert_query_fallback = "INSERT INTO users (fullname, email, password, age, gender, role, created_at) 
                                            VALUES ('$name', '$email', '$hashed_password', '$age', '$gender', '$role', NOW())";
                    if(mysqli_query($conn, $insert_query_fallback)){
                        $success = "Account created successfully! You can now login.";
                    } else {
                        $error = "Registration failed. Database error: " . mysqli_error($conn);
                    }
                } else {
                    $error = "Registration failed. Please try again. Error: " . $db_error;
                }
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
    <title>VitalBalance | Create Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/design-system-v2.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h1>VitalBalance</h1>
        <p class="tagline">Create Your Account</p>

        <?php if(isset($error) && $error != "") { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <?php if(isset($success) && $success != "") { ?>
            <div class="success-msg">
                <?php echo $success; ?>
            </div>
        <?php } ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required 
                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            
            <input type="email" name="email" placeholder="Email Address" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            
            <input type="password" name="password" placeholder="Password (min. 6 characters)" required>
            
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

            <input type="number" name="age" placeholder="Age" min="1" max="150" required
                   value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>">

            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>

            <select name="role">
                <option value="user" <?php echo (!isset($_POST['role']) || $_POST['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>

            <button type="submit" name="register">Create Account</button>
        </form>

        <div class="links">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>
</div>

</body>
</html>

