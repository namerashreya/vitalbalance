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

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $target_id = intval($_GET['id']);
    // Update remedies to remove association if any
    mysqli_query($conn, "UPDATE remedies SET disease_id = NULL WHERE disease_id = $target_id");
    mysqli_query($conn, "DELETE FROM diseases WHERE disease_id = $target_id");
    header("Location: diseases.php?msg=Disease deleted successfully");
    exit();
}

// Handle Add/Edit Action
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['disease_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    if (isset($_POST['disease_id']) && !empty($_POST['disease_id'])) {
        // Update
        $did = intval($_POST['disease_id']);
        $sql = "UPDATE diseases SET disease_name='$name', category='$category', description='$description' WHERE disease_id=$did";
        $msg = "Disease updated successfully";
    } else {
        // Insert
        $sql = "INSERT INTO diseases (disease_name, category, description) VALUES ('$name', '$category', '$description')";
        $msg = "Disease added successfully";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: diseases.php?msg=$msg");
        exit();
    }
}

// Get all diseases with remedies count
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = " WHERE (d.disease_name LIKE '%$search%' OR d.category LIKE '%$search%')";
}

$diseases_query = "SELECT d.*, COUNT(r.remedy_id) as remedy_count 
                   FROM diseases d 
                   LEFT JOIN remedies r ON d.disease_id = r.disease_id 
                   $search_query 
                   GROUP BY d.disease_id 
                   ORDER BY d.disease_name ASC";
$diseases_result = mysqli_query($conn, $diseases_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Disease Management</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .disease-table-card {
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
            flex-wrap: wrap;
        }
        .search-bar input {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            flex: 1;
            font-size: 14px;
            min-width: 200px;
        }
        .badge-cat {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background: var(--secondary-sage-ultra-light);
            color: var(--primary-green-dark);
            text-transform: uppercase;
        }
        .badge-count {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            background: #f0f9ff;
            color: #0369a1;
            font-weight: 600;
        }
        .action-btns { display: flex; gap: 8px; justify-content: center; }
        .btn-sm {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-edit { background: #f3f4f6; color: #4b5563; }
        .btn-delete { background: #fee2e2; color: #991b1b; }

        /* Modal Styles */
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
        .disease-modal {
            background: white;
            width: 90%;
            max-width: 600px;
            border-radius: 20px;
            padding: 30px;
            position: relative;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
        }
        .form-group textarea { height: 120px; resize: vertical; }
    </style>
</head>
<body>
    <!-- Shared Admin Header -->
    <?php include('../includes/admin_header.php'); ?>

    <main class="main-container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; gap: 20px; flex-wrap: wrap;">
            <div>
                <h1 class="page-title" style="margin-bottom: 0;">Disease Management</h1>
                <p class="page-subtitle">Manage the database of chronic conditions and their classifications</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <?php if(isset($_GET['msg'])): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 10px 20px; border-radius: 8px; font-size: 14px;">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                    </div>
                <?php endif; ?>
                <button onclick="openAddModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Disease
                </button>
            </div>
        </div>

        <div class="disease-table-card">
            <form action="diseases.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Search by name or category..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
                <?php if(isset($_GET['search'])): ?>
                    <a href="diseases.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 25%;">Disease Name</th>
                        <th style="width: 15%;">Category</th>
                        <th style="width: 30%;">Description</th>
                        <th style="width: 100px; text-align: center;">Remedies</th>
                        <th style="width: 120px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($diseases_result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($diseases_result)): ?>
                            <tr>
                                <td style="color: #64748b;">#<?php echo $row['disease_id']; ?></td>
                                <td style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($row['disease_name']); ?></td>
                                <td><span class="badge-cat"><?php echo htmlspecialchars($row['category'] ?: 'Uncategorized'); ?></span></td>
                                <td style="color: #64748b; font-size: 13px; max-width: 300px;">
                                    <?php echo (strlen($row['description']) > 80) ? substr(htmlspecialchars($row['description']), 0, 80).'...' : htmlspecialchars($row['description']); ?>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge-count">
                                        <i class="fas fa-leaf" style="font-size: 10px; margin-right: 4px;"></i>
                                        <?php echo $row['remedy_count']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button onclick="editDisease(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn-sm btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="diseases.php?action=delete&id=<?php echo $row['disease_id']; ?>" 
                                           class="btn-sm btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this disease? Linked remedies will be unlinked (but not deleted).')"
                                           title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                                <i class="fas fa-search" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
                                No diseases found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Add/Edit Modal -->
    <div id="diseaseModal" class="modal-overlay">
        <div class="disease-modal">
            <span style="position: absolute; top: 20px; right: 20px; cursor: pointer; font-size: 24px; color: #94a3b8;" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle" style="margin-bottom: 25px; color: #1e293b;">Add New Disease</h2>
            
            <form action="diseases.php" method="POST">
                <input type="hidden" name="disease_id" id="disease_id">
                <div class="form-group">
                    <label>Disease Name</label>
                    <input type="text" name="disease_name" id="disease_name" required placeholder="e.g. Type 2 Diabetes">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" id="category" placeholder="e.g. Metabolic, Cardiovascular, Respiratory">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description" required placeholder="Provide a brief overview of the condition..."></textarea>
                </div>
                <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Disease</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Add New Disease';
            document.getElementById('disease_id').value = '';
            document.getElementById('disease_name').value = '';
            document.getElementById('category').value = '';
            document.getElementById('description').value = '';
            document.getElementById('diseaseModal').style.display = 'flex';
        }

        function editDisease(disease) {
            document.getElementById('modalTitle').innerText = 'Edit Disease';
            document.getElementById('disease_id').value = disease.disease_id;
            document.getElementById('disease_name').value = disease.disease_name;
            document.getElementById('category').value = disease.category;
            document.getElementById('description').value = disease.description;
            document.getElementById('diseaseModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('diseaseModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('diseaseModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
