<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

$user_id = $_SESSION['user_id'];

// Get user information
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Handle profile update
$update_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $weight = mysqli_real_escape_string($conn, $_POST['weight']);
    $height = mysqli_real_escape_string($conn, $_POST['height']);
    $emergency_contact_name = mysqli_real_escape_string($conn, $_POST['emergency_contact_name']);
    $emergency_contact_phone = mysqli_real_escape_string($conn, $_POST['emergency_contact_phone']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    
    // Avatar upload logic
    $avatar_sql = "";
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['avatar']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = "avatar_" . $user_id . "_" . time() . "." . $ext;
            $upload_path = "../uploads/avatars/" . $new_filename;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                // Delete old avatar if exists
                if (!empty($user['avatar']) && file_exists("../uploads/avatars/" . $user['avatar'])) {
                    unlink("../uploads/avatars/" . $user['avatar']);
                }
                $avatar_sql = ", avatar = '$new_filename'";
            }
        }
    }

    // Check if dob is empty, set to NULL if it is
    $dob_val = !empty($dob) ? "'$dob'" : "NULL";
    $weight_val = !empty($weight) ? "'$weight'" : "NULL";
    $height_val = !empty($height) ? "'$height'" : "NULL";

    $update_query = "UPDATE users SET 
                    name = '$name', 
                    email = '$email', 
                    phone = '$phone', 
                    address = '$address', 
                    gender = '$gender', 
                    dob = $dob_val, 
                    blood_group = '$blood_group', 
                    weight = $weight_val, 
                    height = $height_val, 
                    emergency_contact_name = '$emergency_contact_name', 
                    emergency_contact_phone = '$emergency_contact_phone', 
                    bio = '$bio' 
                    $avatar_sql
                    WHERE user_id = $user_id";
                    
    if (mysqli_query($conn, $update_query)) {
        $update_message = '<div id="success-alert" style="background: #dcfce7; color: #166534; padding: 16px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 12px; animation: slideIn 0.5s ease;">
            <span style="font-size: 20px;">✓</span> 
            <div>
                <strong style="display: block;">Success!</strong>
                <span style="font-size: 14px;">Your profile has been updated successfully.</span>
            </div>
        </div>';
        // Refresh user data
        $user_result = mysqli_query($conn, $user_query);
        $user = mysqli_fetch_assoc($user_result);
        $_SESSION['name'] = $user['name']; // Update session name
    } else {
        $update_message = '<div id="error-alert" style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #fecaca; display: flex; align-items: center; gap: 12px; animation: slideIn 0.5s ease;">
            <span style="font-size: 20px;">✗</span>
            <div>
                <strong style="display: block;">Update Failed</strong>
                <span style="font-size: 14px;">Error: ' . mysqli_error($conn) . '</span>
            </div>
        </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | My Profile</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: var(--gradient-primary);
            --glass-bg: var(--bg-white);
            --border-color: rgba(45, 90, 39, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-beige);
        }

        .profile-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
            margin-top: 24px;
        }

        @media (max-width: 992px) {
            .profile-layout {
                grid-template-columns: 1fr;
            }
        }

        .profile-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .profile-card-static {
            background: var(--glass-bg);
            border-radius: 24px;
            padding: 32px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
            position: relative;
        }

        .avatar-container {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 20px;
        }

        .large-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            font-weight: 700;
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
            border: 4px solid white;
            overflow: hidden;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
            border: 4px solid transparent;
        }

        .avatar-container:hover .avatar-overlay {
            opacity: 1;
        }

        .avatar-overlay i {
            font-size: 24px;
        }

        .profile-name-title {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .profile-email-sub {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .profile-stat-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .profile-stat-item:last-child {
            border-bottom: none;
        }

        .profile-stat-label {
            color: #64748b;
            font-size: 13px;
        }

        .profile-stat-value {
            font-weight: 600;
            color: #0f172a;
            font-size: 13px;
        }

        .form-section {
            background: white;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .input-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 600px) {
            .input-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: #fff;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .btn-update {
            background: var(--primary-green);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
            font-size: 16px;
            width: fit-content;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4);
        }

        #avatar-preview-container {
            display: none;
            margin-bottom: 10px;
            color: #667eea;
            font-size: 12px;
            font-weight: 600;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <!-- Shared Header & Nav -->
    <?php include('../includes/header.php'); ?>

    <!-- Main Content -->
    <main class="main-container">
        <h1 class="page-title">My Profile</h1>
        <p class="page-subtitle">Personalize your journey and keep your health information updated.</p>

        <?php echo $update_message; ?>

        <div class="profile-layout">
            <!-- Profile Form Start -->
            <form method="POST" action="" enctype="multipart/form-data" id="profileForm" style="display: contents;">
                <!-- Sidebar -->
                <aside class="profile-sidebar">
                    <div class="profile-card-static">
                        <div class="avatar-container" onclick="document.getElementById('avatar-input').click();">
                            <div class="large-avatar" id="avatar-display">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="../uploads/avatars/<?php echo $user['avatar']; ?>" style="width:100%;height:100%;object-fit:cover;">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                <?php endif; ?>
                            </div>
                            <div class="avatar-overlay">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                        <input type="file" name="avatar" id="avatar-input" style="display: none;" accept="image/*" onchange="previewAvatar(this)">
                        <div id="avatar-preview-container">✨ New photo selected!</div>
                        
                        <h2 class="profile-name-title"><?php echo htmlspecialchars($user['name']); ?></h2>
                        <p class="profile-email-sub"><?php echo htmlspecialchars($user['email']); ?></p>
                        
                        <div style="margin-top: 24px; text-align: left;">
                            <div class="profile-stat-item">
                                <span class="profile-stat-label">Member Since</span>
                                <span class="profile-stat-value"><?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                            </div>
                            <div class="profile-stat-item">
                                <span class="profile-stat-label">Username</span>
                                <span class="profile-stat-value">@<?php echo htmlspecialchars($user['username'] ?? 'user'); ?></span>
                            </div>
                            <div class="profile-stat-item">
                                <span class="profile-stat-label">Status</span>
                                <span class="profile-stat-value" style="color: #10b981;">● Active</span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-card-static" style="padding: 16px;">
                        <h3 style="font-family: 'Outfit'; font-size: 16px; margin-bottom: 16px;">Quick Stats</h3>
                        <?php
                        $diseases_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM user_diseases WHERE user_id = $user_id"))['count'] ?? 0;
                        $records_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM health_records WHERE user_id = $user_id"))['count'] ?? 0;
                        ?>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div style="background: #f8fafc; padding: 12px; border-radius: 12px; text-align: center;">
                                <div style="font-size: 20px; font-weight: 700; color: #667eea;"><?php echo $diseases_count; ?></div>
                                <div style="font-size: 10px; color: #64748b; text-transform: uppercase;">Diseases</div>
                            </div>
                            <div style="background: #f8fafc; padding: 12px; border-radius: 12px; text-align: center;">
                                <div style="font-size: 20px; font-weight: 700; color: #667eea;"><?php echo $records_count; ?></div>
                                <div style="font-size: 10px; color: #64748b; text-transform: uppercase;">Records</div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Profile Form Body -->
                <div class="profile-form-body">
                    <!-- Basic Info -->
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">👤</div>
                            <h2 class="section-title">Basic Information</h2>
                        </div>
                        <div class="input-grid">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+1 (555) 000-0000" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="dob" value="<?php echo $user['dob'] ?? ''; ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="prefer_not_to_say" <?php echo ($user['gender'] ?? '') == 'prefer_not_to_say' ? 'selected' : ''; ?>>Prefer not to say</option>
                                    <option value="male" <?php echo ($user['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo ($user['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="other" <?php echo ($user['gender'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Blood Group</label>
                                <select name="blood_group" class="form-control">
                                    <option value="">Select...</option>
                                    <?php 
                                    $groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                    foreach($groups as $g) {
                                        $selected = ($user['blood_group'] ?? '') == $g ? 'selected' : '';
                                        echo "<option value='$g' $selected>$g</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Health Body Details -->
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">⚖️</div>
                            <h2 class="section-title">Physical Metrics</h2>
                        </div>
                        <div class="input-grid">
                            <div class="form-group">
                                <label>Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" value="<?php echo $user['weight'] ?? ''; ?>" placeholder="e.g. 70.5" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Height (cm)</label>
                                <input type="number" step="0.1" name="height" value="<?php echo $user['height'] ?? ''; ?>" placeholder="e.g. 175" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">🚨</div>
                            <h2 class="section-title">Emergency Contact</h2>
                        </div>
                        <div class="input-grid">
                            <div class="form-group">
                                <label>Contact Name</label>
                                <input type="text" name="emergency_contact_name" value="<?php echo htmlspecialchars($user['emergency_contact_name'] ?? ''); ?>" placeholder="Full Name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" value="<?php echo htmlspecialchars($user['emergency_contact_phone'] ?? ''); ?>" placeholder="Phone Number" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Biography / Address -->
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">📝</div>
                            <h2 class="section-title">Bio & Address</h2>
                        </div>
                        <div style="display: grid; gap: 20px;">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" placeholder="Your full address..."><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Bio / Health Notes</label>
                                <textarea name="bio" class="form-control" placeholder="A little about yourself or any special health notes..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom: 40px; display: flex; gap: 16px;">
                        <button type="submit" name="update_profile" class="btn-update">
                            Update My Profile
                        </button>
                        <a href="dashboard.php" class="btn-update" style="background: #e2e8f0; color: #475569; box-shadow: none; text-decoration: none; display: flex; align-items: center;">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const display = document.getElementById('avatar-display');
                    display.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
                    document.getElementById('avatar-preview-container').style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Auto-hide alert after 5 seconds
        setTimeout(() => {
            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('error-alert');
            if (successAlert) successAlert.style.display = 'none';
            if (errorAlert) errorAlert.style.display = 'none';
        }, 5000);
    </script>
</body>
</html>
