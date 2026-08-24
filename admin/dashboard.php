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

// Get user information
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get statistics with error handling
$total_users = 0;
$total_users_query = "SELECT COUNT(*) as count FROM users WHERE role = 'user'";
$total_users_result = @mysqli_query($conn, $total_users_query);
if($total_users_result) {
    $total_users = mysqli_fetch_assoc($total_users_result)['count'];
}

$total_diseases = 0;
$total_diseases_query = "SELECT COUNT(*) as count FROM diseases";
$total_diseases_result = @mysqli_query($conn, $total_diseases_query);
if($total_diseases_result) {
    $total_diseases = mysqli_fetch_assoc($total_diseases_result)['count'];
}

$total_remedies = 0;
$total_remedies_query = "SELECT COUNT(*) as count FROM remedies";
$total_remedies_result = @mysqli_query($conn, $total_remedies_query);
if($total_remedies_result) {
    $total_remedies = mysqli_fetch_assoc($total_remedies_result)['count'];
}

$total_records = 0;
$total_records_query = "SELECT COUNT(*) as count FROM health_records";
$total_records_result = @mysqli_query($conn, $total_records_query);
if($total_records_result) {
    $total_records = mysqli_fetch_assoc($total_records_result)['count'];
}

// Get recent users
$recent_users_query = "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5";
$recent_users_result = @mysqli_query($conn, $recent_users_query);
if(!$recent_users_result) {
    $recent_users_result = mysqli_query($conn, "SELECT 1 WHERE 1=0"); // Empty result set
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <!-- Shared Admin Header -->
    <?php include('../includes/admin_header.php'); ?>

    <!-- Main Content -->
    <main class="main-container">
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="page-subtitle">Manage your integrated wellness system for personalized chronic disease management</p>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Users</span>
                    <div class="stat-icon">👥</div>
                </div>
                <div class="stat-value"><?php echo $total_users; ?></div>
                <div class="stat-change">Active wellness members</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Diseases</span>
                    <div class="stat-icon">🏥</div>
                </div>
                <div class="stat-value"><?php echo $total_diseases; ?></div>
                <div class="stat-change">Chronic conditions tracked</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Natural Remedies</span>
                    <div class="stat-icon">🌿</div>
                </div>
                <div class="stat-value"><?php echo $total_remedies; ?></div>
                <div class="stat-change">Treatment options available</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Health Records</span>
                    <div class="stat-icon">📋</div>
                </div>
                <div class="stat-value"><?php echo $total_records; ?></div>
                <div class="stat-change">Total records tracked</div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Recent Users -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Users</h2>
                    <a href="users.php" class="btn btn-secondary">View All</a>
                </div>
                <?php if(mysqli_num_rows($recent_users_result) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($recent_user = mysqli_fetch_assoc($recent_users_result)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($recent_user['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($recent_user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($recent_user['status'] == 'active') ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 10px; padding: 2px 8px;">
                                            <?php echo ucfirst($recent_user['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($recent_user['created_at'])); ?></td>
                                    <td>
                                        <a href="users.php?view=<?php echo $recent_user['user_id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px;">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">👥</div>
                        <h3>No users yet</h3>
                        <p>Users will appear here once they register</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Quick Actions</h2>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 20px;">
                    <a href="diseases.php" class="btn btn-primary" style="text-align: center; width: 100%;">Add Disease</a>
                    <a href="remedies.php" class="btn btn-primary" style="text-align: center; width: 100%;">Add Remedy</a>
                    <a href="users.php" class="btn btn-primary" style="text-align: center; width: 100%;">Add User</a>
                    <a href="reports.php" class="btn btn-secondary" style="text-align: center; width: 100%;">View Reports</a>
                </div>
            </div>
        </div>

        </div>
    </main>
</body>
</html>

