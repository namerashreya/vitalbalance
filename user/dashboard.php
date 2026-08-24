<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

$user_id = $_SESSION['user_id'];

// Get user information
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get user's chronic diseases count
$disease_count = 0;
$disease_query = "SELECT COUNT(*) as count FROM user_diseases WHERE user_id = $user_id";
$disease_result = @mysqli_query($conn, $disease_query);
if($disease_result) {
    $disease_count = mysqli_fetch_assoc($disease_result)['count'];
}

// Get health records count
$records_count = 0;
$records_query = "SELECT COUNT(*) as count FROM health_records WHERE user_id = $user_id";
$records_result = @mysqli_query($conn, $records_query);
if($records_result) {
    $records_count = mysqli_fetch_assoc($records_result)['count'];
}

// Get recent remedies (with error handling for missing columns/tables)
$remedies_result = false;
// First check if table exists
$table_check = @mysqli_query($conn, "SHOW TABLES LIKE 'remedies'");
if($table_check && mysqli_num_rows($table_check) > 0) {
    // Table exists, check for created_at column
    $check_column = @mysqli_query($conn, "SHOW COLUMNS FROM remedies LIKE 'created_at'");
    $order_by = ($check_column && mysqli_num_rows($check_column) > 0) ? "created_at DESC" : "remedy_id DESC";
    
    $remedies_query = "SELECT * FROM remedies ORDER BY $order_by LIMIT 5";
    $remedies_result = @mysqli_query($conn, $remedies_query);
}

if(!$remedies_result) {
    // Table doesn't exist or query failed, create empty result
    $remedies_result = mysqli_query($conn, "SELECT 1 WHERE 1=0"); // Empty result set
}

// Get user's diseases with proper column checking
$user_diseases_query = "SELECT ud.*, d.disease_name";
// Check if description column exists
$desc_check = @mysqli_query($conn, "SHOW COLUMNS FROM diseases LIKE 'description'");
if ($desc_check && mysqli_num_rows($desc_check) > 0) {
    $user_diseases_query .= ", d.description";
}
$user_diseases_query .= " FROM user_diseases ud 
                       JOIN diseases d ON ud.disease_id = d.disease_id 
                       WHERE ud.user_id = $user_id";
// Check if created_at exists in user_diseases
$created_at_check = @mysqli_query($conn, "SHOW COLUMNS FROM user_diseases LIKE 'created_at'");
if ($created_at_check && mysqli_num_rows($created_at_check) > 0) {
    $user_diseases_query .= " ORDER BY ud.created_at DESC";
} else {
    $user_diseases_query .= " ORDER BY d.disease_name ASC";
}
$user_diseases_result = @mysqli_query($conn, $user_diseases_query);
// Check if wellness_scores table exists and calculate or fetch the latest score
function calculateWellnessScore($conn, $user_id) {
    $score = 100;
    
    // Get all health records
    $records_query = "SELECT record_type, value, recorded_at 
                      FROM health_records 
                      WHERE user_id = $user_id 
                      ORDER BY recorded_at DESC 
                      LIMIT 30";
    $records_result = @mysqli_query($conn, $records_query);
    
    if (!$records_result || mysqli_num_rows($records_result) == 0) {
        return 75; // Default score
    }
    
    $recent_records = [];
    while ($record = mysqli_fetch_assoc($records_result)) {
        $recent_records[] = $record;
    }
    
    // BP check
    $bp_records = array_filter($recent_records, function($r) { return $r['record_type'] == 'blood_pressure' || $r['record_type'] == 'vitals'; });
    if (count($bp_records) > 0) {
        $latest_bp = reset($bp_records);
        if (preg_match('/(\d+)\/(\d+)/', $latest_bp['value'], $matches)) {
            $systolic = (int)$matches[1];
            $diastolic = (int)$matches[2];
            if ($systolic > 140 || $diastolic > 90) $score -= 15;
            elseif ($systolic < 90 || $diastolic < 60) $score -= 10;
        }
    }
    
    // Disease check
    $disease_query = "SELECT COUNT(*) as count FROM user_diseases WHERE user_id = $user_id AND status = 'active'";
    $disease_result = @mysqli_query($conn, $disease_query);
    if ($disease_result) {
        $active_diseases = mysqli_fetch_assoc($disease_result)['count'];
        $score -= ($active_diseases * 5);
    }
    
    return max(0, min(100, round($score)));
}

$wellness_score = calculateWellnessScore($conn, $user_id);
if(!$user_diseases_result) {
    $user_diseases_result = mysqli_query($conn, "SELECT 1 WHERE 1=0"); // Empty result set
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Dashboard</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: var(--bg-beige);
            font-family: var(--font-body);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Action Buttons Styles */
        .remedy-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .remedy-item-card:hover .remedy-actions {
            opacity: 1;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-update {
            background-color: #10b981;
            color: white;
            border: none;
        }

        .btn-update:hover {
            background-color: #059669; /* Darker green on hover */
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-delete {
            background-color: #ef4444;
            color: white;
            border: none;
        }

        .btn-delete:hover {
            background-color: #dc2626; /* Darker red on hover */
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        /* Layout Fixes */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
            min-height: 80vh;
        }

        .content-grid {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
        }

        .card {
            width: 100%;
        }

        /* Custom Modal Styles */
        .vb-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .vb-modal.active {
            display: flex;
        }

        .vb-modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            animation: modalSlideUp 0.3s ease;
        }

        @keyframes modalSlideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .vb-modal-header {
            margin-bottom: 20px;
        }

        .vb-modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
        }

        .vb-modal-body {
            margin-bottom: 24px;
        }

        .vb-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #10b981;
        }

        /* Featured Remedies Styles */
        .featured-remedy-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: 1px solid #f1f5f9;
        }

        .featured-remedy-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(16, 185, 129, 0.15);
        }

        .featured-remedy-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .featured-remedy-card:hover .featured-remedy-img {
            transform: scale(1.1);
        }

        .featured-remedy-img-wrapper {
            position: relative;
            overflow: hidden;
            height: 180px;
        }

        .featured-remedy-category {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 2;
        }

        .featured-remedy-content {
            padding: 16px;
        }

        .featured-remedy-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .featured-remedy-desc {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .featured-remedy-actions {
            display: flex;
            gap: 8px;
        }

        .featured-remedy-btn {
            flex: 1;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-add-remedy {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-add-remedy:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-view-remedy {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-view-remedy:hover {
            background: #e2e8f0;
        }

        /* Toast Animations */
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Shared Header & Nav -->
    <?php include('../includes/header.php'); ?>


    <!-- Main Content -->
    <main class="main-container">
        <div style="background: linear-gradient(135deg, #10b981, #059669); padding: 30px; border-radius: 16px; color: white; margin-bottom: 32px; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.2);">
            <h1 style="font-size: 32px; margin: 0 0 8px 0; font-weight: 800; letter-spacing: -0.5px;">Welcome Back, <?php echo htmlspecialchars($user['name']); ?>! <i class="fas fa-hand-sparkles" style="color: #fbbf24;"></i></h1>
            <p style="font-size: 18px; margin: 0; opacity: 0.95; font-weight: 500;"><i class="fas fa-heart" style="color: #fecaca; margin-right: 6px;"></i> Keep it up, you will recover soon!</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Active Conditions</span>
                    <div class="stat-icon"><i class="fas fa-hospital-user"></i></div>
                </div>
                <div class="stat-value"><?php echo $disease_count; ?></div>
                <div class="stat-change">Manage your chronic diseases</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark)); color: white;">
                <div class="stat-header">
                    <span class="stat-title" style="color: white; opacity: 0.9;">Health Records</span>
                    <div class="stat-icon" style="opacity: 0.2;"><i class="fas fa-file-medical"></i></div>
                </div>
                <div class="stat-value" style="color: white;"><?php echo $records_count; ?></div>
                <div class="stat-change" style="color: rgba(255,255,255,0.8);">Track your health metrics</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, var(--secondary-sage), var(--secondary-sage-dark)); color: white;">
                <div class="stat-header">
                    <span class="stat-title" style="color: white; opacity: 0.9;">Wellness Score</span>
                    <div class="stat-icon" style="opacity: 0.2;"><i class="fas fa-heart-pulse"></i></div>
                </div>
                <div class="stat-value" style="color: white;"><?php echo $wellness_score; ?>%</div>
                <div class="stat-change" style="color: rgba(255,255,255,0.8);">Personalized health index</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, var(--accent-gold), #B8860B); color: white; cursor: pointer;" onclick="window.open('report.php', '_blank')">
                <div class="stat-header">
                    <span class="stat-title" style="color: white; opacity: 0.9;">Health Report</span>
                    <div class="stat-icon" style="opacity: 0.2;"><i class="fas fa-file-medical"></i></div>
                </div>
                <div class="stat-value" style="color: white; font-size: 28px;">GENERATE</div>
                <div class="stat-change" style="color: rgba(255,255,255,0.8);">Professional PDF Analysis</div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- My Chronic Diseases -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">My Chronic Diseases</h2>
                    <a href="my_diseases.php" class="btn btn-secondary">View All</a>
                </div>
                <?php if($user_diseases_result && mysqli_num_rows($user_diseases_result) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Disease</th>
                                <th>Status</th>
                                <th>Diagnosed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            mysqli_data_seek($user_diseases_result, 0);
                            $count = 0;
                            while(($disease = mysqli_fetch_assoc($user_diseases_result)) !== false && $count < 5): 
                                $count++;
                                // Safely get values with defaults
                                $disease_name = isset($disease['disease_name']) ? htmlspecialchars($disease['disease_name']) : 'Unknown';
                                $status = isset($disease['status']) ? $disease['status'] : 'active';
                                $diagnosed_date = isset($disease['diagnosed_date']) && !empty($disease['diagnosed_date']) ? $disease['diagnosed_date'] : null;
                            ?>
                                <tr>
                                    <td><strong><?php echo $disease_name; ?></strong></td>
                                    <td><span class="badge badge-<?php echo $status == 'active' ? 'danger' : ($status == 'managed' ? 'success' : 'warning'); ?>">
                                        <?php echo ucfirst($status); ?>
                                    </span></td>
                                    <td><?php echo $diagnosed_date ? date('M d, Y', strtotime($diagnosed_date)) : 'Not specified'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-notes-medical"></i></div>
                        <h3>No chronic diseases recorded</h3>
                        <p>Start tracking your health conditions to get personalized recommendations</p>
                        <a href="add_disease.php" class="btn btn-primary">Add Disease</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Natural Remedies -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-leaf" style="color: var(--primary-green);"></i> My Natural Remedies</h2>
                </div>
                
                <!-- Add Natural Remedy Form -->
                <form id="addRemedyForm" style="margin-bottom: 20px; padding: 20px; background: #f8fafc; border-radius: 12px;">
                    <div style="display: grid; gap: 12px;">
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                Natural Remedy
                            </label>
                            <input type="text" 
                                   name="remedy_name" 
                                   id="remedy_name"
                                   placeholder="e.g., Ginger tea, Turmeric, Meditation" 
                                   required
                                   style="width: 100%; padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                Benefits / Notes
                            </label>
                            <textarea name="remedy_notes" 
                                      id="remedy_notes"
                                      placeholder="How does this help you?" 
                                      rows="2"
                                      style="width: 100%; padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px; resize: vertical;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: fit-content;">
                            <span id="addRemedyBtnText">+ Add Remedy</span>
                        </button>
                    </div>
                </form>

                <!-- Display User's Natural Remedies -->
                <div id="userRemediesList">
                    <!-- Will be populated by JavaScript -->
                    <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                        <div style="font-size: 48px; margin-bottom: 12px;"><i class="fas fa-leaf" style="color: var(--primary-green-light);"></i></div>
                        <p>No natural remedies added yet. Add your first remedy above!</p>
                    </div>
                </div>
            </div>

            <!-- Featured Remedies from Library -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-star" style="color: #f59e0b;"></i> Featured Natural Remedies</h2>
                    <a href="remedies.php" class="btn btn-secondary">Browse All</a>
                </div>
                
                <div id="featuredRemediesGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 20px;">
                    <!-- Will be populated by JavaScript -->
                    <div style="text-align: center; padding: 40px 20px; color: #64748b; grid-column: 1 / -1;">
                        <div style="font-size: 48px; margin-bottom: 12px;"><i class="fas fa-spinner fa-spin" style="color: var(--primary-green);"></i></div>
                        <p>Loading featured remedies...</p>
                    </div>
                </div>
        <!-- Health Report Highlights -->
        <div class="card" style="background: linear-gradient(135deg, #1B3C1A 0%, #2D5A27 100%); margin-bottom: 32px; border: none; position: relative; overflow: hidden; padding: 40px; color: white;">
            <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            <div style="position: relative; z-index: 1;">
                <h2 style="color: white; font-size: 28px; font-weight: 800; margin-bottom: 12px;">Intelligent Progress Report</h2>
                <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin-bottom: 24px; max-width: 600px;">
                    Get a comprehensive analysis of your health journey. Our AI-driven report summarizes your chronic conditions, wellness trends, and natural remedy effectiveness in a medical-grade PDF.
                </p>
                <div style="display: flex; gap: 16px;">
                    <a href="report.php" target="_blank" class="btn btn-primary" style="background: var(--accent-gold); color: #1B3C1A; border: none; padding: 12px 30px; border-radius: 12px; font-weight: 700; text-decoration: none;">
                        Download Report PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quick Actions</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
                <a href="add_disease.php" class="btn btn-primary" style="text-align: center;">Add Disease</a>
                <a href="health_records.php?action=add" class="btn btn-primary" style="text-align: center;">Record Health Data</a>
                <a href="remedies.php" class="btn btn-primary" style="text-align: center;">Browse Remedies</a>
                <a href="wellness_tracker.php" class="btn btn-primary" style="text-align: center;">Wellness Tracker</a>
            </div>
        </div>
    </main>

    <!-- Update Remedy Modal -->
    <div id="updateRemedyModal" class="vb-modal">
        <div class="vb-modal-content">
            <div class="vb-modal-header">
                <h2 class="vb-modal-title">Edit Natural Remedy</h2>
            </div>
            <div class="vb-modal-body">
                <form id="updateRemedyForm">
                    <input type="hidden" name="id" id="edit_remedy_id">
                    <div class="form-group">
                        <label for="edit_remedy_name">Remedy Name</label>
                        <input type="text" id="edit_remedy_name" name="remedy_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_remedy_notes">Benefits / Notes</label>
                        <textarea id="edit_remedy_notes" name="remedy_notes" class="form-control" rows="4"></textarea>
                    </div>
                </form>
            </div>
            <div class="vb-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeUpdateModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitUpdateRemedy()">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="vb-modal">
        <div class="vb-modal-content">
            <div class="vb-modal-header">
                <h2 class="vb-modal-title">Confirm Deletion</h2>
            </div>
            <div class="vb-modal-body">
                <p style="color: #64748b;">Are you sure you want to remove "<span id="deleteRemedyName" style="font-weight: 700; color: #1e293b;"></span>" from your tracker? This action cannot be undone.</p>
            </div>
            <div class="vb-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" style="background-color: #ef4444; border-color: #ef4444; color: white;">Delete Remedy</button>
            </div>
        </div>
    </div>

    <script>
        // Load user's natural remedies on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadUserRemedies();
            loadFeaturedRemedies();
        });

        // Load Featured Remedies from Database
        function loadFeaturedRemedies() {
            fetch('../api/get_featured_remedies.php')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('featuredRemediesGrid');
                    
                    if (data.success && data.remedies && data.remedies.length > 0) {
                        let html = '';
                        
                        data.remedies.forEach(remedy => {
                            html += `
                                <div class="featured-remedy-card">
                                    <div class="featured-remedy-img-wrapper">
                                        <img src="${remedy.image}" class="featured-remedy-img" 
                                             onerror="this.onerror=null;this.src='https://placehold.co/400x300/e2e8f0/475569?text=${encodeURIComponent(remedy.remedy_name)}'"
                                             alt="${escapeHtml(remedy.remedy_name)}">
                                        <span class="featured-remedy-category">${escapeHtml(remedy.category)}</span>
                                    </div>
                                    <div class="featured-remedy-content">
                                        <h3 class="featured-remedy-title">${escapeHtml(remedy.remedy_name)}</h3>
                                        <p class="featured-remedy-desc">${escapeHtml(remedy.description)}</p>
                                        <div class="featured-remedy-actions">
                                            <button class="featured-remedy-btn btn-add-remedy" 
                                                    onclick="quickAddRemedy('${remedy.remedy_name.replace(/'/g, "\\'")}', '${(remedy.description || '').replace(/'/g, "\\'")}', '${(remedy.instructions || '').replace(/'/g, "\\'")}')">
                                                <i class="fas fa-plus"></i> Add to Tracker
                                            </button>
                                            <button class="featured-remedy-btn btn-view-remedy" onclick="window.location.href='remedies.php'">
                                                <i class="fas fa-eye"></i> View All
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                            <div style="text-align: center; padding: 40px 20px; color: #64748b; grid-column: 1 / -1;">
                                <div style="font-size: 48px; margin-bottom: 12px;">🌿</div>
                                <p>No remedies available yet.</p>
                                <a href="remedies.php" class="btn btn-primary" style="margin-top: 16px; display: inline-block;">Browse Remedies</a>
                            </div>
                        `;
                    }
                })
                .catch(err => {
                    console.error('Error loading featured remedies:', err);
                    document.getElementById('featuredRemediesGrid').innerHTML = `
                        <div style="text-align: center; padding: 40px 20px; color: #ef4444; grid-column: 1 / -1;">
                            <p>Failed to load featured remedies.</p>
                        </div>
                    `;
                });
        }

        // Quick Add Remedy to User's Tracker
        function quickAddRemedy(name, description, instructions) {
            const formData = new FormData();
            formData.append('remedy_name', name);
            formData.append('remedy_notes', description + (instructions ? '\n\nInstructions: ' + instructions : ''));

            fetch('../api/add_user_remedy.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // Show success message
                    const successMsg = document.createElement('div');
                    successMsg.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 16px 24px; border-radius: 12px; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); z-index: 9999; animation: slideInRight 0.3s ease;';
                    successMsg.innerHTML = '<i class="fas fa-check-circle"></i> Remedy added to your tracker!';
                    document.body.appendChild(successMsg);
                    
                    setTimeout(() => {
                        successMsg.style.animation = 'slideOutRight 0.3s ease';
                        setTimeout(() => successMsg.remove(), 300);
                    }, 3000);
                    
                    // Reload user remedies
                    loadUserRemedies();
                } else {
                    alert('Error: ' + (data.message || 'Failed to add remedy'));
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Failed to add remedy. Please try again.');
            });
        }


        // Handle add remedy form submission
        document.getElementById('addRemedyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btnText = document.getElementById('addRemedyBtnText');
            const originalText = btnText.textContent;
            
            btnText.textContent = 'Adding...';
            
            fetch('../api/add_user_remedy.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btnText.textContent = '✓ Added!';
                    this.reset();
                    loadUserRemedies();
                    
                    setTimeout(() => {
                        btnText.textContent = originalText;
                    }, 2000);
                } else {
                    alert('Error: ' + (data.message || 'Failed to add remedy'));
                    btnText.textContent = originalText;
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Failed to add remedy. Please try again.');
                btnText.textContent = originalText;
            });
        });

        // Load and display user's natural remedies
        function loadUserRemedies() {
            fetch('../api/get_user_remedies.php')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('userRemediesList');
                    
                    if (data.success && data.remedies && data.remedies.length > 0) {
                        let html = '<div style="display: grid; gap: 12px;">';
                        
                        data.remedies.forEach(remedy => {
                            // Extract just the instructions if possible, or show all notes
                            const safeNotes = escapeHtml(remedy.remedy_notes || '');
                            const isDetailed = safeNotes.includes('Instructions:');
                            
                            html += `
                                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; transition: all 0.3s ease; border-left: 4px solid #10b981; position: relative;" class="remedy-item-card">
                                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                                        <div style="flex: 1; cursor: pointer;" onclick="toggleRemedyDetails(this)">
                                            <h4 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                                🌿 ${escapeHtml(remedy.remedy_name)}
                                                <i class="fas fa-chevron-down" style="font-size: 12px; color: #94a3b8; transition: transform 0.3s;"></i>
                                            </h4>
                                            <div style="background: #f0fdf4; color: #166534; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 99px; display: inline-block; margin-bottom: 8px;">Added to Tracker</div>
                                            
                                            <small style="display: block; margin-top: 4px; font-size: 12px; color: #94a3b8;">
                                                Added ${formatDate(remedy.created_at)}
                                            </small>

                                            <div class="remedy-actions">
                                                <button onclick="openUpdateModal(${remedy.id}, '${remedy.remedy_name.replace(/'/g, "\\'")}', \`${(remedy.remedy_notes || '').replace(/`/g, "\\`")}\`, event)" 
                                                        class="action-btn btn-update">
                                                    <i class="fas fa-edit"></i> Update
                                                </button>
                                                <button onclick="confirmDelete(${remedy.id}, '${remedy.remedy_name.replace(/'/g, "\\'")}', event)" 
                                                        class="action-btn btn-delete">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        html += '</div>';
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                                <div style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;">🌿</div>
                                <p style="font-size: 16px; font-weight: 500;">No natural remedies added yet.</p>
                                <p style="font-size: 14px; margin-top: 8px;">Browse our library to add some!</p>
                                <a href="remedies.php" class="btn btn-secondary" style="margin-top: 16px; display: inline-block; font-size: 14px;">Browse Library</a>
                            </div>
                        `;
                    }
                })
                .catch(err => {
                    console.error('Error loading remedies:', err);
                });
        }

        function toggleRemedyDetails(element) {
            const details = element.querySelector('.remedy-details');
            const icon = element.querySelector('.fa-chevron-down');
            
            if (details.style.display === 'none') {
                details.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
                details.style.animation = 'fadeIn 0.3s ease';
            } else {
                details.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Update functionality
        function openUpdateModal(id, name, notes, event) {
            if(event) event.stopPropagation();
            document.getElementById('edit_remedy_id').value = id;
            document.getElementById('edit_remedy_name').value = name;
            document.getElementById('edit_remedy_notes').value = notes;
            document.getElementById('updateRemedyModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeUpdateModal() {
            document.getElementById('updateRemedyModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function submitUpdateRemedy() {
            const form = document.getElementById('updateRemedyForm');
            const formData = new FormData(form);
            
            const saveBtn = document.querySelector('.vb-modal-footer .btn-primary');
            const originalText = saveBtn.textContent;
            saveBtn.textContent = 'Saving...';
            saveBtn.disabled = true;

            fetch('../api/update_user_remedy.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    closeUpdateModal();
                    loadUserRemedies();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Failed to update remedy');
            })
            .finally(() => {
                saveBtn.textContent = originalText;
                saveBtn.disabled = false;
            });
        }

        // Delete confirmation functionality
        let remedyIdToDelete = null;

        function confirmDelete(id, name, event) {
            if(event) event.stopPropagation();
            remedyIdToDelete = id;
            document.getElementById('deleteRemedyName').textContent = name;
            document.getElementById('deleteConfirmModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            
            document.getElementById('confirmDeleteBtn').onclick = function() {
                deleteRemedy(remedyIdToDelete);
            };
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            remedyIdToDelete = null;
        }

        // Modified Delete a remedy
        function deleteRemedy(id) {
            const btn = document.getElementById('confirmDeleteBtn');
            const originalText = btn.textContent;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            btn.disabled = true;
            
            fetch('../api/delete_user_remedy.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id: id})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeDeleteModal();
                    loadUserRemedies();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete remedy'));
                    btn.textContent = originalText;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Failed to delete remedy. Please try again.');
                btn.textContent = originalText;
                btn.disabled = false;
            });
        }


        // Helper functions
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays === 0) return 'today';
            if (diffDays === 1) return 'yesterday';
            if (diffDays < 7) return `${diffDays} days ago`;
            if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
            
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
    </script>
    <!-- Shared Footer -->
    <?php include('../includes/footer.php'); ?>
</body>
</html>

