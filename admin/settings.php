<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

// Check if user is admin
if($_SESSION['role'] != 'admin'){
    header("Location: ../user/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get current admin information
$admin_query = "SELECT * FROM users WHERE user_id = $user_id";
$admin_result = mysqli_query($conn, $admin_query);
$admin = mysqli_fetch_assoc($admin_result);

$msg = "";
$msg_type = "";

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_general'])) {
        foreach ($_POST['settings'] as $key => $value) {
            $key = mysqli_real_escape_string($conn, $key);
            $value = mysqli_real_escape_string($conn, $value);
            mysqli_query($conn, "INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$value') ON DUPLICATE KEY UPDATE setting_value = '$value'");
        }
        $msg = "System settings updated successfully";
        $msg_type = "success";
    }

    if (isset($_POST['update_profile'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        
        $sql = "UPDATE users SET name='$name', email='$email', phone='$phone' WHERE user_id=$user_id";
        if (mysqli_query($conn, $sql)) {
            $msg = "Profile updated successfully";
            $msg_type = "success";
            // Update current admin variable
            $admin['name'] = $name;
            $admin['email'] = $email;
            $admin['phone'] = $phone;
        }
    }

    if (isset($_POST['update_password'])) {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if (password_verify($current_pass, $admin['password'])) {
            if ($new_pass === $confirm_pass) {
                $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE users SET password='$hashed_pass' WHERE user_id=$user_id");
                $msg = "Password changed successfully";
                $msg_type = "success";
            } else {
                $msg = "New passwords do not match";
                $msg_type = "danger";
            }
        } else {
            $msg = "Incorrect current password";
            $msg_type = "danger";
        }
    }
}

// Fetch all settings
$settings = [];
$settings_res = mysqli_query($conn, "SELECT * FROM settings");
while ($row = mysqli_fetch_assoc($settings_res)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | System Settings</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .settings-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-top: 20px;
        }
        .settings-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
        }
        .form-group input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i { color: #3b82f6; }
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-item:last-child { border-bottom: none; }
        .info-label { color: #64748b; font-size: 13px; }
        .info-value { color: #1e293b; font-weight: 600; font-size: 13px; }
    </style>
</head>
<body>
    <!-- Shared Admin Header -->
    <?php include('../includes/admin_header.php'); ?>

    <main class="main-container">
        <h1 class="page-title">System Settings</h1>
        <p class="page-subtitle">Configure application preferences and manage your administrator profile</p>

        <?php if($msg): ?>
            <div class="alert alert-<?php echo $msg_type; ?>">
                <i class="fas <?php echo $msg_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="settings-grid">
            <div class="left-col">
                <!-- General Settings -->
                <div class="settings-card">
                    <h2 class="section-title"><i class="fas fa-cog"></i> General Configuration</h2>
                    <form action="settings.php" method="POST">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Site Name</label>
                                <input type="text" name="settings[site_name]" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'VitalBalance'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Support & Contact Email</label>
                                <input type="email" name="settings[contact_email]" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Maintenance Mode</label>
                                <select name="settings[maintenance_mode]">
                                    <option value="off" <?php echo ($settings['maintenance_mode'] ?? '') == 'off' ? 'selected' : ''; ?>>Disabled (Site Live)</option>
                                    <option value="on" <?php echo ($settings['maintenance_mode'] ?? '') == 'on' ? 'selected' : ''; ?>>Enabled (Site Offline)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>User Registration</label>
                                <select name="settings[allow_registration]">
                                    <option value="on" <?php echo ($settings['allow_registration'] ?? '') == 'on' ? 'selected' : ''; ?>>Allowed</option>
                                    <option value="off" <?php echo ($settings['allow_registration'] ?? '') == 'off' ? 'selected' : ''; ?>>Disabled</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="update_general" class="btn btn-primary">Save System Changes</button>
                    </form>
                </div>

                <!-- Admin Profile -->
                <div class="settings-card">
                    <h2 class="section-title"><i class="fas fa-user-shield"></i> Administrator Profile</h2>
                    <form action="settings.php" method="POST">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($admin['phone']); ?>">
                            </div>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">Update My Profile</button>
                    </form>
                </div>
            </div>

            <div class="right-col">
                <!-- Change Password -->
                <div class="settings-card">
                    <h2 class="section-title"><i class="fas fa-lock"></i> Security</h2>
                    <form action="settings.php" method="POST">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="update_password" class="btn btn-secondary" style="width: 100%;">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
