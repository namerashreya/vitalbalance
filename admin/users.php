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

// Handle Actions (Role Change / Deactivation)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $target_id = intval($_GET['id']);
    
    // Prevent admin from deactivating themselves
    if ($target_id != $user_id) {
        if ($_GET['action'] == 'toggle_status') {
            $status_query = "SELECT status FROM users WHERE user_id = $target_id";
            $status_res = mysqli_query($conn, $status_query);
            $current_status = mysqli_fetch_assoc($status_res)['status'];
            $new_status = ($current_status == 'active') ? 'inactive' : 'active';
            
            mysqli_query($conn, "UPDATE users SET status = '$new_status' WHERE user_id = $target_id");
            header("Location: users.php?msg=Status updated successfully");
            exit();
        }
        
        if ($_GET['action'] == 'change_role' && isset($_GET['role'])) {
            $new_role = mysqli_real_escape_string($conn, $_GET['role']);
            if (in_array($new_role, ['user', 'admin'])) {
                mysqli_query($conn, "UPDATE users SET role = '$new_role' WHERE user_id = $target_id");
                header("Location: users.php?msg=Role updated successfully");
                exit();
            }
        }
    } else {
        header("Location: users.php?error=You cannot modify your own account status or role from here");
        exit();
    }
}

// Get all users
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR username LIKE '%$search%')";
}

$users_query = "SELECT * FROM users WHERE 1=1 $search_query ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | User Management</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-top: 20px;
        }
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .search-bar input {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            flex: 1;
            font-size: 14px;
        }
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-active { background: var(--secondary-sage-ultra-light); color: var(--primary-green); }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .badge-admin { background: var(--primary-green-soft); color: var(--primary-green-dark); }
        .badge-user { background: var(--bg-beige); color: var(--text-secondary); }
        
        .action-btns {
            display: flex;
            gap: 8px;
        }
        .btn-sm {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-role { background: #f3f4f6; color: #4b5563; }
        .btn-role:hover { background: #e5e7eb; }
        .btn-toggle { background: #ecfdf5; color: #059669; }
        .btn-toggle.inactive { background: #fff1f2; color: #e11d48; }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }
        .user-modal {
            background: white;
            width: 90%;
            max-width: 600px;
            border-radius: 20px;
            padding: 30px;
            position: relative;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            cursor: pointer;
            font-size: 20px;
            color: #94a3b8;
        }
        .modal-header-info {
            display: flex;
            gap: 20px;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 20px;
        }
        .modal-avatar {
            width: 80px;
            height: 80px;
            background: #6366f1;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .detail-item label {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .detail-item p {
            color: #1e293b;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Shared Admin Header -->
    <?php include('../includes/admin_header.php'); ?>

    <!-- Main Content -->
    <main class="main-container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
            <div>
                <h1 class="page-title" style="margin-bottom: 0;">User Management</h1>
                <p class="page-subtitle">Oversee all registered members and manage account privileges</p>
            </div>
            <?php if(isset($_GET['msg'])): ?>
                <div style="background: #dcfce7; color: #166534; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500;">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="user-table-card">
            <form action="users.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Search by name, email or username..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
                <?php if(isset($_GET['search'])): ?>
                    <a href="users.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($users_result)): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 32px; height: 32px; background: #6366f1; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">
                                        <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($row['name']); ?></div>
                                        <div style="font-size: 12px; color: #64748b;">@<?php echo htmlspecialchars($row['username']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="color: #475569;"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="badge-status <?php echo ($row['role'] == 'admin') ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo ucfirst($row['role']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-status <?php echo ($row['status'] == 'active') ? 'badge-active' : 'badge-inactive'; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td style="color: #64748b; font-size: 13px;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <div class="action-btns">
                                    <button onclick="viewUser(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn-sm btn-role" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <?php if($row['user_id'] != $user_id): ?>
                                        <a href="users.php?action=change_role&id=<?php echo $row['user_id']; ?>&role=<?php echo ($row['role'] == 'admin') ? 'user' : 'admin'; ?>" 
                                           class="btn-sm btn-role" title="Switch Role to <?php echo ($row['role'] == 'admin') ? 'User' : 'Admin'; ?>">
                                            <i class="fas fa-user-tag"></i>
                                        </a>
                                        <a href="users.php?action=toggle_status&id=<?php echo $row['user_id']; ?>" 
                                           class="btn-sm btn-toggle <?php echo ($row['status'] == 'active') ? 'active' : 'inactive'; ?>" 
                                           title="<?php echo ($row['status'] == 'active') ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas <?php echo ($row['status'] == 'active') ? 'fa-user-check' : 'fa-user-slash'; ?>"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- User Details Modal -->
    <div id="userModal" class="modal-overlay">
        <div class="user-modal">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <div class="modal-header-info">
                <div id="modalAvatar" class="modal-avatar">U</div>
                <div>
                    <h2 id="modalName" style="color: #1e293b; margin-bottom: 5px;">User Name</h2>
                    <p id="modalEmail" style="color: #64748b; font-size: 14px;">email@example.com</p>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Username</label>
                    <p id="modalUsername">@username</p>
                </div>
                <div class="detail-item">
                    <label>Phone</label>
                    <p id="modalPhone">Not provided</p>
                </div>
                <div class="detail-item">
                    <label>Age / Gender</label>
                    <p id="modalAgeGender">25 / Male</p>
                </div>
                <div class="detail-item">
                    <label>Blood Group</label>
                    <p id="modalBlood">O+</p>
                </div>
                <div class="detail-item">
                    <label>Joined Since</label>
                    <p id="modalJoined">Jan 01, 2024</p>
                </div>
                <div class="detail-item">
                    <label>Bio</label>
                    <p id="modalBio">No bio available</p>
                </div>
            </div>
            <div class="detail-item" style="margin-top: 20px;">
                <label>Address</label>
                <p id="modalAddress">No address provided</p>
            </div>
        </div>
    </div>

    <script>
        function viewUser(user) {
            document.getElementById('modalAvatar').innerText = user.name.charAt(0).toUpperCase();
            document.getElementById('modalName').innerText = user.name;
            document.getElementById('modalEmail').innerText = user.email;
            document.getElementById('modalUsername').innerText = '@' + user.username;
            document.getElementById('modalPhone').innerText = user.phone || 'Not provided';
            document.getElementById('modalAgeGender').innerText = (user.age || 'N/A') + ' / ' + (user.gender || 'N/A');
            document.getElementById('modalBlood').innerText = user.blood_group || 'N/A';
            document.getElementById('modalJoined').innerText = new Date(user.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('modalBio').innerText = user.bio || 'No bio available';
            document.getElementById('modalAddress').innerText = user.address || 'No address provided';
            
            document.getElementById('userModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        // Close on click outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('userModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
