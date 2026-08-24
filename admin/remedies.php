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
    mysqli_query($conn, "DELETE FROM remedies WHERE remedy_id = $target_id");
    header("Location: remedies.php?msg=Remedy deleted successfully");
    exit();
}

// Handle Add/Edit Action
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['remedy_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $ingredients = mysqli_real_escape_string($conn, $_POST['ingredients']);
    $instructions = mysqli_real_escape_string($conn, $_POST['instructions']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);
    
    $disease_id = !empty($_POST['disease_id']) ? intval($_POST['disease_id']) : 'NULL';
    
    if (isset($_POST['remedy_id']) && !empty($_POST['remedy_id'])) {
        // Update
        $rid = intval($_POST['remedy_id']);
        $sql = "UPDATE remedies SET remedy_name='$name', category='$category', description='$description', ingredients='$ingredients', instructions='$instructions', image='$image', disease_id=$disease_id WHERE remedy_id=$rid";
        $msg = "Remedy updated successfully";
    } else {
        // Insert
        $sql = "INSERT INTO remedies (remedy_name, category, description, ingredients, instructions, image, approved_by_admin, disease_id) VALUES ('$name', '$category', '$description', '$ingredients', '$instructions', '$image', 1, $disease_id)";
        $msg = "Remedy added successfully";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: remedies.php?msg=$msg");
        exit();
    }
}

// Get all remedies
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = " WHERE (r.remedy_name LIKE '%$search%' OR r.category LIKE '%$search%')";
}

$remedies_query = "SELECT r.*, d.disease_name FROM remedies r LEFT JOIN diseases d ON r.disease_id = d.disease_id $search_query ORDER BY r.remedy_id DESC";
$remedies_result = mysqli_query($conn, $remedies_query);

// Get list of diseases for dropdown
$all_diseases = mysqli_query($conn, "SELECT disease_id, disease_name FROM diseases ORDER BY disease_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Remedies Management</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .remedy-table-card {
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
        .remedy-modal {
            background: white;
            width: 90%;
            max-width: 700px;
            border-radius: 20px;
            padding: 30px;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
        .form-group textarea { height: 80px; resize: vertical; }
        .full-width { grid-column: span 2; }
    </style>
</head>
<body>
    <!-- Shared Admin Header -->
    <?php include('../includes/admin_header.php'); ?>

    <main class="main-container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; gap: 20px; flex-wrap: wrap;">
            <div>
                <h1 class="page-title" style="margin-bottom: 0;">Remedies Management</h1>
                <p class="page-subtitle">Add, edit, or remove natural remedies from the database</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <?php if(isset($_GET['msg'])): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 10px 20px; border-radius: 8px; font-size: 14px;">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                    </div>
                <?php endif; ?>
                <button onclick="openAddModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Remedy
                </button>
            </div>
        </div>

        <div class="remedy-table-card">
            <form action="remedies.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Search by name or category..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
                <?php if(isset($_GET['search'])): ?>
                    <a href="remedies.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 25%;">Remedy Name</th>
                        <th style="width: 20%;">Category</th>
                        <th style="width: 25%;">Linked Disease</th>
                        <th style="width: 100px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($remedies_result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($remedies_result)): ?>
                            <tr>
                                <td style="color: #64748b;">#<?php echo $row['remedy_id']; ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <?php if($row['image']): ?>
                                            <img src="<?php echo $row['image']; ?>" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; background: #eee;">
                                        <?php else: ?>
                                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                                <i class="fas fa-leaf"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($row['remedy_name']); ?></div>
                                    </div>
                                </td>
                                <td><span class="badge-cat"><?php echo htmlspecialchars($row['category']); ?></span></td>
                                <td style="color: #64748b; font-size: 13px;">
                                    <?php echo $row['disease_name'] ? '<i class="fas fa-notes-medical" style="margin-right:5px;"></i>'.htmlspecialchars($row['disease_name']) : '<span style="color:#cbd5e1;">None</span>'; ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button onclick="editRemedy(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn-sm btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="remedies.php?action=delete&id=<?php echo $row['remedy_id']; ?>" 
                                           class="btn-sm btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this remedy?')"
                                           title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">
                                <i class="fas fa-search" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
                                No remedies found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Add/Edit Modal -->
    <div id="remedyModal" class="modal-overlay">
        <div class="remedy-modal">
            <span style="position: absolute; top: 20px; right: 20px; cursor: pointer; font-size: 24px; color: #94a3b8;" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle" style="margin-bottom: 25px; color: #1e293b;">Add New Remedy</h2>
            
            <form action="remedies.php" method="POST">
                <input type="hidden" name="remedy_id" id="remedy_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Remedy Name</label>
                        <input type="text" name="remedy_name" id="remedy_name" required placeholder="e.g. Honey & Lemon Tea">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" id="category" required placeholder="e.g. Cold & Flu">
                    </div>
                    <div class="form-group full-width">
                        <label>Image URL</label>
                        <input type="text" name="image" id="image" placeholder="../assets/images/remedies/image_name.png">
                    </div>
                    <div class="form-group full-width">
                        <label>Short Description</label>
                        <textarea name="description" id="description" required placeholder="Describe the health benefits..."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Ingredients (Comma separated)</label>
                        <textarea name="ingredients" id="ingredients" required placeholder="e.g. 1 tbsp Honey, 1/2 Lemon, Warm Water"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Instructions</label>
                        <textarea name="instructions" id="instructions" required placeholder="Step by step preparation..."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Link to Chronic Disease (Optional)</label>
                        <select name="disease_id" id="disease_id_select">
                            <option value="">-- No Specific Disease --</option>
                            <?php mysqli_data_seek($all_diseases, 0); while($d = mysqli_fetch_assoc($all_diseases)): ?>
                                <option value="<?php echo $d['disease_id']; ?>"><?php echo htmlspecialchars($d['disease_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Remedy</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Add New Remedy';
            document.getElementById('remedy_id').value = '';
            document.getElementById('remedy_name').value = '';
            document.getElementById('category').value = '';
            document.getElementById('image').value = '';
            document.getElementById('description').value = '';
            document.getElementById('ingredients').value = '';
            document.getElementById('instructions').value = '';
            document.getElementById('disease_id_select').value = '';
            document.getElementById('remedyModal').style.display = 'flex';
        }

        function editRemedy(remedy) {
            document.getElementById('modalTitle').innerText = 'Edit Remedy';
            document.getElementById('remedy_id').value = remedy.remedy_id;
            document.getElementById('remedy_name').value = remedy.remedy_name;
            document.getElementById('category').value = remedy.category;
            document.getElementById('image').value = remedy.image;
            document.getElementById('description').value = remedy.description;
            document.getElementById('ingredients').value = remedy.ingredients;
            document.getElementById('instructions').value = remedy.instructions;
            document.getElementById('disease_id_select').value = remedy.disease_id || '';
            document.getElementById('remedyModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('remedyModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('remedyModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
