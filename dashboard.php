<?php
session_start();
include("includes/db.php");
include("includes/auth.php");

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

// Calculate wellness score dynamically
function calculateWellnessScore($conn, $user_id) {
    $score = 100; // Start with perfect score
    
    // Get all health records
    $records_query = "SELECT record_type, value, unit, recorded_at 
                      FROM health_records 
                      WHERE user_id = $user_id 
                      ORDER BY recorded_at DESC 
                      LIMIT 30";
    $records_result = @mysqli_query($conn, $records_query);
    
    if (!$records_result || mysqli_num_rows($records_result) == 0) {
        return 75; // Default score if no records
    }
    
    $recent_records = [];
    while ($record = mysqli_fetch_assoc($records_result)) {
        $recent_records[] = $record;
    }
    
    // Analyze blood pressure
    $bp_records = array_filter($recent_records, function($r) {
        return $r['record_type'] == 'blood_pressure';
    });
    if (count($bp_records) > 0) {
        $latest_bp = reset($bp_records);
        $bp_value = $latest_bp['value'];
        if (preg_match('/(\d+)\/(\d+)/', $bp_value, $matches)) {
            $systolic = (int)$matches[1];
            $diastolic = (int)$matches[2];
            if ($systolic > 140 || $diastolic > 90) {
                $score -= 15;
            } elseif ($systolic < 90 || $diastolic < 60) {
                $score -= 10;
            } elseif ($systolic > 120 || $diastolic > 80) {
                $score -= 5;
            }
        }
    }
    
    // Analyze blood sugar
    $bs_records = array_filter($recent_records, function($r) {
        return $r['record_type'] == 'blood_sugar';
    });
    if (count($bs_records) > 0) {
        $latest_bs = reset($bs_records);
        $bs_value = (float)str_replace(['mg/dL', 'mmol/L'], '', $latest_bs['value']);
        if ($bs_value > 125) {
            $score -= 20;
        } elseif ($bs_value > 100) {
            $score -= 10;
        } elseif ($bs_value < 70) {
            $score -= 15;
        }
    }
    
    // Check active diseases
    $disease_query = "SELECT COUNT(*) as count FROM user_diseases WHERE user_id = $user_id AND status = 'active'";
    $disease_result = @mysqli_query($conn, $disease_query);
    if ($disease_result) {
        $active_diseases = mysqli_fetch_assoc($disease_result)['count'];
        $score -= ($active_diseases * 5);
    }
    
    return max(0, min(100, round($score)));
}

$wellness_score = calculateWellnessScore($conn, $user_id);

// Get recipes count
$recipes_count = 0;
$recipes_query = "SELECT COUNT(*) as count FROM recipes";
$recipes_result = @mysqli_query($conn, $recipes_query);
if($recipes_result) {
    $recipes_count = mysqli_fetch_assoc($recipes_result)['count'];
}

// Get trending/common diseases
$trending_diseases_query = "SELECT * FROM diseases ORDER BY trend_percentage DESC, prevalence_rate DESC LIMIT 8";
$trending_diseases_result = mysqli_query($conn, $trending_diseases_query);

// Get recent health records
$recent_records_query = "SELECT * FROM health_records WHERE user_id = $user_id ORDER BY recorded_at DESC LIMIT 5";
$recent_records_result = mysqli_query($conn, $recent_records_query);

// Get recipes
$recipes_list_query = "SELECT * FROM recipes ORDER BY created_at DESC LIMIT 6";
$recipes_list_result = mysqli_query($conn, $recipes_list_query);

// Get user's diseases
$user_diseases_query = "SELECT ud.*, d.disease_name, d.description, d.icon, d.color_code 
                       FROM user_diseases ud 
                       JOIN diseases d ON ud.disease_id = d.disease_id 
                       WHERE ud.user_id = $user_id 
                       ORDER BY ud.created_at DESC";
$user_diseases_result = mysqli_query($conn, $user_diseases_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - VitalBalance</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        /* Sidebar Styles */
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 1100;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 2rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .sidebar-menu {
            padding: 1.5rem 0;
            flex: 1;
            overflow-y: auto;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 2rem;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .menu-item:hover {
            background: #f9fafb;
            color: #2E7D32;
        }
        
        .menu-item.active {
            background: #f0fdf4;
            color: #2E7D32;
            border-right: 4px solid #2E7D32;
        }
        
        .menu-item i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .main-container {
            flex: 1;
            margin-left: 280px;
            width: calc(100% - 280px);
        }
        
        @media (max-width: 968px) {
            .mobile-toggle {
                display: block !important;
            }
            .sidebar {
                transform: translateX(-100%);
                z-index: 1200;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-container {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h1 class="logo">VitalBalance</h1>
                <span class="logo-subtitle">Health Assistant</span>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="my_diseases.php" class="menu-item">
                    <i class="fas fa-stethoscope"></i> My Diseases
                </a>
                <a href="health_records.php" class="menu-item">
                    <i class="fas fa-clipboard-list"></i> Health Records
                </a>
                <a href="user/remedies.php" class="menu-item">
                    <i class="fas fa-leaf"></i> Natural Remedies
                </a>
                <a href="wellness_tracker.php" class="menu-item">
                    <i class="fas fa-heartbeat"></i> wellness Tracker
                </a>
                <div class="menu-divider" style="height: 1px; background: #f3f4f6; margin: 1rem 0;"></div>
                <a href="user/report.php" target="_blank" class="menu-item" style="color: #6d28d9;">
                    <strong>Health Report</strong>
                </a>
                <a href="settings.php" class="menu-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </nav>
            <div class="sidebar-footer" style="padding: 1.5rem 2rem; border-top: 1px solid #f3f4f6;">
                <a href="logout.php" class="menu-item" style="padding: 0; color: #ef4444;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-container">
            <!-- Navigation Header -->
            <nav class="navbar" id="navbar" style="position: sticky; width: 100%; left: 0;">
                <div class="nav-container">
                    <div class="nav-left">
                        <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('active')" style="display: none; background: none; border: none; font-size: 1.5rem; cursor: pointer;">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
            
                <div class="nav-right">
                    <div class="nav-notification">
                        <button class="notification-btn" id="notificationBtn">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                    </div>
                    
                    <div class="nav-profile">
                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="profile-trigger">
                                <div class="profile-avatar">
                                    <?php echo strtoupper(substr($user['name'] ?? $user['username'] ?? 'U', 0, 1)); ?>
                                </div>
                                <div class="profile-info">
                                    <span class="profile-name"><?php echo htmlspecialchars($user['name'] ?? $user['username'] ?? 'User'); ?></span>
                                    <span class="profile-email"><?php echo htmlspecialchars($user['email']); ?></span>
                                </div>
                            </div>
                            <div class="dropdown-menu">
                                <a href="profile.php" class="dropdown-item">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                                <a href="my_diseases.php" class="dropdown-item">
                                    <i class="fas fa-stethoscope"></i> My Conditions
                                </a>
                                <a href="health_records.php" class="dropdown-item">
                                    <i class="fas fa-clipboard-list"></i> Health Records
                                </a>
                                <a href="settings.php" class="dropdown-item">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="user/report.php" target="_blank" class="dropdown-item">
                                    Health Report
                                </a>
                                <a href="logout.php" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content" style="margin-top: 0;">
        <!-- Welcome Section -->
        <section class="welcome-section" data-aos="fade-up">
            <h1 class="welcome-title">
                Welcome back, <span class="gradient-text"><?php echo htmlspecialchars(explode(' ', $user['name'] ?? $user['username'] ?? 'User')[0]); ?>!</span> 👋
            </h1>
            <p class="welcome-subtitle">Your personalized wellness dashboard for chronic disease management</p>
        </section>

        <!-- Stats Cards Grid -->
        <section class="stats-section">
            <div class="stats-grid">
                <!-- Active Conditions Card -->
                <div class="stat-card" data-aos="fade-up" data-aos-delay="100" data-filter="disease" data-filter-value="all" onclick="openAddDiseaseModal()">
                    <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #ff6b6b, #ee5a6f);">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-label">Active Conditions</h3>
                        <p class="stat-value" data-count="<?php echo $disease_count; ?>" id="diseaseCount">0</p>
                        <p class="stat-description">Manage your chronic diseases</p>
                    </div>
                    <div class="stat-hover-effect"></div>
                </div>

                <!-- Health Records Card -->
                <div class="stat-card" data-aos="fade-up" data-aos-delay="200" data-filter="record_type" data-filter-value="all" onclick="filterDashboard('record_type', 'all')">
                    <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #4ecdc4, #44a08d);">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-label">Health Records</h3>
                        <p class="stat-value" data-count="<?php echo $records_count; ?>">0</p>
                        <p class="stat-description">Track your health metrics</p>
                    </div>
                    <div class="stat-hover-effect"></div>
                </div>

                <!-- Wellness Score Card -->
                <div class="stat-card wellness-card" data-aos="fade-up" data-aos-delay="300" onclick="showHealthChart()">
                    <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #a8e6cf, #88d8a3);">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-label">Wellness Score</h3>
                        <div class="wellness-progress">
                            <div class="progress-ring-wrapper">
                                <svg class="progress-ring" width="120" height="120">
                                    <circle class="progress-ring-circle-bg" cx="60" cy="60" r="54"></circle>
                                    <circle class="progress-ring-circle" cx="60" cy="60" r="54" 
                                            data-percentage="<?php echo $wellness_score; ?>" id="wellnessCircle"></circle>
                                </svg>
                                <div class="progress-text">
                                    <span class="progress-value" id="wellnessScoreValue"><?php echo $wellness_score; ?>%</span>
                                </div>
                            </div>
                        </div>
                        <p class="stat-description" id="wellnessTrend">Based on your records</p>
                    </div>
                    <div class="stat-hover-effect"></div>
                </div>

                <!-- Food Recipes Card -->
                <div class="stat-card" data-aos="fade-up" data-aos-delay="400" onclick="window.location.href='#recipes-section'">
                    <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #ffe66d, #ffd93d);">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-label">Food Recipes</h3>
                        <p class="stat-value" data-count="<?php echo $recipes_count; ?>">0</p>
                        <p class="stat-description">Satvic & healthy recipes</p>
                    </div>
                    <div class="stat-hover-effect"></div>
                </div>

                <!-- Health Report Card -->
                <div class="stat-card report-card" data-aos="fade-up" data-aos-delay="500" onclick="window.open('user/report.php', '_blank', 'width=1000,height=900')">
                    <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #b794f4, #805ad5);">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-label">Health Report</h3>
                        <p class="stat-value">PDF</p>
                        <p class="stat-description">Download progress summary</p>
                    </div>
                    <div class="stat-hover-effect" style="opacity: 0.1; background: rgba(139, 92, 246, 0.1);"></div>
                </div>
            </div>
        </section>

        <!-- Dashboard Category Quick Links -->
        <section class="category-nav-section" data-aos="fade-up" style="margin-bottom: 2rem;">
            <div style="display: flex; gap: 1rem; overflow-x: auto; padding: 0.5rem 0;">
                <a href="#myDiseasesSection" class="btn btn-outline" style="white-space: nowrap; border-radius: 99px; border-color: #e5e7eb; color: #4b5563;">
                    <i class="fas fa-stethoscope"></i> My Conditions
                </a>
                <a href="#healthRecordsSection" class="btn btn-outline" style="white-space: nowrap; border-radius: 99px; border-color: #e5e7eb; color: #4b5563;">
                    <i class="fas fa-clipboard-list"></i> Health Records
                </a>
                <a href="user/remedies.php" class="btn btn-outline" style="white-space: nowrap; border-radius: 99px; border-color: #e5e7eb; color: #4b5563;">
                    <i class="fas fa-leaf"></i> Browse Remedies
                </a>
                <a href="user/report.php" target="_blank" class="btn btn-primary" style="white-space: nowrap; border-radius: 99px; background: #6d28d9;">
                    Generate Progress Report
                </a>
            </div>
        </section>

        <!-- My Chronic Diseases Section -->
        <section class="my-diseases-section" data-aos="fade-up" data-aos-delay="150" id="myDiseasesSection">
            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        <i class="fas fa-stethoscope"></i>
                        My Chronic Diseases
                    </h2>
                    <p class="section-subtitle">Manage your health conditions</p>
                </div>
                <button class="btn-primary" onclick="openAddDiseaseModal()">
                    <i class="fas fa-plus"></i> Add Disease
                </button>
            </div>

            <div class="my-diseases-container" id="myDiseasesContainer">
                <?php if(mysqli_num_rows($user_diseases_result) > 0): ?>
                <div class="my-diseases-grid">
                    <?php 
                    mysqli_data_seek($user_diseases_result, 0);
                    while($user_disease = mysqli_fetch_assoc($user_diseases_result)):
                        $status_colors = [
                            'active' => '#ef4444',
                            'managed' => '#f59e0b',
                            'recovered' => '#10b981'
                        ];
                        $status_color = $status_colors[$user_disease['status']] ?? '#6b7280';
                    ?>
                    <div class="my-disease-card" data-user-disease-id="<?php echo $user_disease['id']; ?>" data-disease-id="<?php echo $user_disease['disease_id']; ?>">
                        <div class="my-disease-header">
                            <div class="my-disease-icon" style="background: <?php echo $user_disease['color_code'] ?? '#8b5cf6'; ?>20; color: <?php echo $user_disease['color_code'] ?? '#8b5cf6'; ?>;">
                                <?php echo $user_disease['icon'] ?? '🩺'; ?>
                            </div>
                            <div class="my-disease-status" style="background: <?php echo $status_color; ?>20; color: <?php echo $status_color; ?>;">
                                <?php echo ucfirst($user_disease['status']); ?>
                            </div>
                        </div>
                        <h3 class="my-disease-name"><?php echo htmlspecialchars($user_disease['disease_name']); ?></h3>
                        <?php if($user_disease['diagnosed_date']): ?>
                        <p class="my-disease-date">
                            <i class="fas fa-calendar"></i> 
                            Diagnosed: <?php echo date('M d, Y', strtotime($user_disease['diagnosed_date'])); ?>
                        </p>
                        <?php endif; ?>
                        <?php if($user_disease['notes']): ?>
                        <p class="my-disease-notes"><?php echo htmlspecialchars($user_disease['notes']); ?></p>
                        <?php endif; ?>
                        <div class="my-disease-actions">
                            <button class="btn-icon-small" onclick="editUserDisease(<?php echo $user_disease['id']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon-small" onclick="deleteUserDisease(<?php echo $user_disease['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <h3>No chronic diseases added yet</h3>
                    <p>Start tracking your health conditions to get personalized recommendations</p>
                    <button class="btn-primary" onclick="openAddDiseaseModal()">
                        <i class="fas fa-plus"></i> Add Your First Disease
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Health Analytics & Reports Section -->
        <section class="report-section" data-aos="fade-up" data-aos-delay="220">
            <div class="report-banner" style="background: linear-gradient(135deg, #2E7D32 0%, #1a4d1f 100%); border-radius: 1.5rem; padding: 3rem; color: white; display: flex; align-items: center; justify-content: space-between; overflow: hidden; position: relative;">
                <div style="position: relative; z-index: 2;">
                    <h2 style="font-family: 'Playfair Display', serif; font-size: 2rem; margin-bottom: 0.5rem; color: white;">Intelligent Health Report</h2>
                    <p style="color: rgba(255,255,255,0.8); font-size: 1.1rem; max-width: 500px; margin-bottom: 2rem;">Get an automated summary of your chronic conditions, vitals, and overall wellness progress in a professional PDF format.</p>
                    <button class="btn-primary" style="background: white; color: #1a4d1f; border: none; font-weight: 700; padding: 1rem 2rem; border-radius: 99px;" onclick="window.open('user/report.php', '_blank', 'width=1000,height=900')">
                        <i class="fas fa-download"></i> Generate PDF Report
                    </button>
                </div>

                <!-- Background decor -->
                <div style="position: absolute; top: -20%; right: -10%; width: 400px; height: 400px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            </div>
        </section>

        <!-- Most Common Diseases Section -->
        <section class="common-diseases-section" data-aos="fade-up" data-aos-delay="200">
            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        <i class="fas fa-chart-line"></i>
                        Currently Increasing Common Diseases
                    </h2>
                    <p class="section-subtitle">Track trending health conditions</p>
                </div>
                <button class="btn-secondary" onclick="window.location.href='diseases.php'">
                    View All Trends
                </button>
            </div>

            <div class="disease-grid">
                <?php 
                if(mysqli_num_rows($trending_diseases_result) > 0):
                    while($disease = mysqli_fetch_assoc($trending_diseases_result)):
                        $trend_icon = $disease['trend'] === 'increasing' ? 'fa-arrow-up' : ($disease['trend'] === 'decreasing' ? 'fa-arrow-down' : 'fa-minus');
                        $trend_color = $disease['trend'] === 'increasing' ? '#ef4444' : ($disease['trend'] === 'decreasing' ? '#10b981' : '#6b7280');
                ?>
                <div class="disease-card" data-aos="zoom-in" data-aos-delay="<?php echo (mysqli_num_rows($trending_diseases_result) - $disease['disease_id']) * 50; ?>" 
                     data-disease-id="<?php echo $disease['disease_id']; ?>" 
                     onclick="filterByDisease(<?php echo $disease['disease_id']; ?>, '<?php echo htmlspecialchars($disease['disease_name']); ?>')">
                    <div class="disease-header">
                        <div class="disease-icon" style="background: <?php echo $disease['color_code'] ?? '#8b5cf6'; ?>20; color: <?php echo $disease['color_code'] ?? '#8b5cf6'; ?>;">
                            <?php echo $disease['icon'] ?? '🩺'; ?>
                        </div>
                        <div class="disease-trend" style="color: <?php echo $trend_color; ?>;">
                            <i class="fas <?php echo $trend_icon; ?>"></i>
                            <span><?php echo number_format($disease['trend_percentage'], 1); ?>%</span>
                        </div>
                    </div>
                    <h3 class="disease-name"><?php echo htmlspecialchars($disease['disease_name']); ?></h3>
                    <p class="disease-prevalence">Prevalence: <?php echo number_format($disease['prevalence_rate'], 1); ?>%</p>
                    <div class="disease-chart">
                        <canvas class="disease-chart-canvas" 
                                data-trend="<?php echo $disease['trend_percentage']; ?>"
                                data-prevalence="<?php echo $disease['prevalence_rate']; ?>"></canvas>
                    </div>
                    <button class="btn-learn-more" onclick="event.stopPropagation(); filterByDisease(<?php echo $disease['disease_id']; ?>, '<?php echo htmlspecialchars($disease['disease_name']); ?>')">
                        View Related Data <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <p>No disease data available</p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Health Trends Chart Section -->
        <section class="health-chart-section" data-aos="fade-up" data-aos-delay="250" id="healthChartSection" style="display: none;">
            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        <i class="fas fa-chart-area"></i>
                        Health Trends
                    </h2>
                    <p class="section-subtitle">Visualize your health metrics over time</p>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <select id="chartRecordType" class="btn-secondary" style="padding: 0.5rem 1rem;" onchange="updateHealthChart()">
                        <option value="all">All Records</option>
                        <option value="blood_pressure">Blood Pressure</option>
                        <option value="blood_sugar">Blood Sugar</option>
                        <option value="weight">Weight</option>
                        <option value="heart_rate">Heart Rate</option>
                    </select>
                    <button class="btn-secondary" onclick="document.getElementById('healthChartSection').style.display='none'">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
            <div class="chart-container" style="background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                <canvas id="healthTrendsChart"></canvas>
            </div>
        </section>

        <!-- Health Records Section -->
        <section class="health-records-section" data-aos="fade-up" data-aos-delay="300" id="healthRecordsSection">
            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        <i class="fas fa-clipboard-list"></i>
                        My Health Records
                    </h2>
                    <p class="section-subtitle">Track and monitor your health metrics</p>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <select id="recordFilter" class="btn-secondary" style="padding: 0.5rem 1rem;" onchange="filterRecords(this.value)">
                        <option value="all">All Types</option>
                        <option value="blood_pressure">Blood Pressure</option>
                        <option value="blood_sugar">Blood Sugar</option>
                        <option value="weight">Weight</option>
                        <option value="temperature">Temperature</option>
                        <option value="heart_rate">Heart Rate</option>
                        <option value="medication">Medication</option>
                        <option value="symptoms">Symptoms</option>
                    </select>
                    <button class="btn-primary" onclick="openAddRecordModal()">
                        <i class="fas fa-plus"></i> Add New Record
                    </button>
                </div>
            </div>

            <div class="records-container" id="recordsContainer">
                <?php if(mysqli_num_rows($recent_records_result) > 0): ?>
                <div class="records-timeline">
                    <?php 
                    while($record = mysqli_fetch_assoc($recent_records_result)):
                        $record_icons = [
                            'blood_pressure' => 'fa-heartbeat',
                            'blood_sugar' => 'fa-tint',
                            'weight' => 'fa-weight',
                            'temperature' => 'fa-thermometer-half',
                            'heart_rate' => 'fa-heart',
                            'medication' => 'fa-pills',
                            'symptoms' => 'fa-stethoscope'
                        ];
                        $icon = $record_icons[$record['record_type']] ?? 'fa-clipboard-list';
                    ?>
                    <div class="record-item" data-aos="fade-left" data-record-type="<?php echo $record['record_type']; ?>" data-record-id="<?php echo $record['record_id']; ?>">
                        <div class="record-icon">
                            <i class="fas <?php echo $icon; ?>"></i>
                        </div>
                        <div class="record-content">
                            <div class="record-header">
                                <h4 class="record-type"><?php echo ucfirst(str_replace('_', ' ', $record['record_type'])); ?></h4>
                                <span class="record-date"><?php echo date('M d, Y h:i A', strtotime($record['recorded_at'])); ?></span>
                            </div>
                            <div class="record-value">
                                <span class="value"><?php echo htmlspecialchars($record['value']); ?></span>
                                <?php if($record['unit']): ?>
                                <span class="unit"><?php echo htmlspecialchars($record['unit']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if($record['notes']): ?>
                            <p class="record-notes"><?php echo htmlspecialchars($record['notes']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="record-actions">
                            <button class="btn-icon" onclick="editRecord(<?php echo $record['record_id']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon" onclick="deleteRecord(<?php echo $record['record_id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="records-footer">
                    <a href="health_records.php" class="btn-link">View All Records <i class="fas fa-arrow-right"></i></a>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>No health records yet</h3>
                    <p>Start tracking your health metrics to monitor your wellness journey</p>
                    <button class="btn-primary" onclick="openAddRecordModal()">
                        <i class="fas fa-plus"></i> Record Health Data
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Recipes Section -->
        <section class="recipes-section" id="recipes-section" data-aos="fade-up" data-aos-delay="400">
            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        <i class="fas fa-utensils"></i>
                        Healthy Food Recipes
                    </h2>
                    <p class="section-subtitle">Satvic Movement inspired nutrition</p>
                </div>
            </div>

            <div class="recipe-filters">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="breakfast">Breakfast</button>
                <button class="filter-btn" data-filter="lunch">Lunch</button>
                <button class="filter-btn" data-filter="dinner">Dinner</button>
                <button class="filter-btn" data-filter="snacks">Snacks</button>
                <button class="filter-btn" data-filter="juices">Juices</button>
            </div>

            <div class="recipes-grid">
                <?php 
                if(mysqli_num_rows($recipes_list_result) > 0):
                    while($recipe = mysqli_fetch_assoc($recipes_list_result)):
                ?>
                <div class="recipe-card" data-aos="zoom-in" data-meal-type="<?php echo $recipe['meal_type']; ?>">
                    <div class="recipe-image">
                        <div class="recipe-image-placeholder">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="recipe-overlay">
                            <button class="btn-favorite" onclick="toggleFavorite(<?php echo $recipe['recipe_id']; ?>)">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="recipe-content">
                        <div class="recipe-header">
                            <h3 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <span class="recipe-badge"><?php echo ucfirst($recipe['meal_type']); ?></span>
                        </div>
                        <p class="recipe-description"><?php echo htmlspecialchars(substr($recipe['description'] ?? '', 0, 100)); ?>...</p>
                        <div class="recipe-meta">
                            <span class="meta-item">
                                <i class="fas fa-clock"></i> <?php echo $recipe['prep_time']; ?> min
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-fire"></i> <?php echo $recipe['cook_time']; ?> min
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-signal"></i> <?php echo ucfirst($recipe['difficulty']); ?>
                            </span>
                        </div>
                        <button class="btn-view-recipe" onclick="viewRecipe(<?php echo $recipe['recipe_id']; ?>)">
                            View Recipe <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <div class="empty-state">
                    <i class="fas fa-utensils"></i>
                    <p>No recipes available</p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Quick Actions Bar -->
        <div class="quick-actions-bar">
            <button class="action-btn" onclick="openAddDiseaseModal()">
                <i class="fas fa-plus-circle"></i>
                <span>Add Disease</span>
            </button>
            <button class="action-btn" onclick="openAddRecordModal()">
                <i class="fas fa-heartbeat"></i>
                <span>Record Health</span>
            </button>
            <button class="action-btn" onclick="window.location.href='#recipes-section'">
                <i class="fas fa-utensils"></i>
                <span>Browse Recipes</span>
            </button>
            <button class="action-btn" onclick="window.location.href='wellness_tracker.php'">
                <i class="fas fa-chart-line"></i>
                <span>Tracker</span>
            </button>
            <button class="action-btn" onclick="window.open('user/report.php', '_blank')">
                <i class="fas fa-file-pdf"></i>
                <span>Report</span>
            </button>
        </div>
    </main>

    <!-- Add Disease Modal -->
    <div class="modal" id="addDiseaseModal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2>Add Chronic Disease</h2>
                <button class="modal-close" onclick="closeAddDiseaseModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addDiseaseForm" class="modal-body">
                <div class="form-group">
                    <label>Search Disease</label>
                    <input type="text" id="diseaseSearch" placeholder="Type to search diseases..." 
                           onkeyup="searchDiseases(this.value)" autocomplete="off">
                    <div id="diseaseSearchResults" class="disease-search-results"></div>
                </div>
                
                <div class="form-group">
                    <label>Select Disease</label>
                    <div id="diseaseList" class="disease-selection-grid">
                        <div class="loading-state">
                            <i class="fas fa-spinner fa-spin"></i> Loading diseases...
                        </div>
                    </div>
                </div>
                
                <div id="selectedDiseaseInfo" class="selected-disease-info" style="display: none;">
                    <div class="selected-disease-card">
                        <div class="selected-disease-icon" id="selectedDiseaseIcon"></div>
                        <div class="selected-disease-details">
                            <h3 id="selectedDiseaseName"></h3>
                            <p id="selectedDiseaseDesc"></p>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="disease_id" id="selectedDiseaseId" required>
                
                <div class="form-group">
                    <label>Diagnosed Date</label>
                    <input type="date" name="diagnosed_date" id="diagnosedDate" 
                           max="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="diseaseStatus">
                        <option value="active">Active</option>
                        <option value="managed">Managed</option>
                        <option value="recovered">Recovered</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Notes (Optional)</label>
                    <textarea name="notes" id="diseaseNotes" rows="3" 
                              placeholder="Additional notes about your condition..."></textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeAddDiseaseModal()">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-plus"></i> Add Disease
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Record Modal -->
    <div class="modal" id="addRecordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Health Record</h2>
                <button class="modal-close" onclick="closeAddRecordModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addRecordForm" class="modal-body">
                <div class="form-group">
                    <label>Record Type</label>
                    <select name="record_type" required>
                        <option value="">Select type</option>
                        <option value="blood_pressure">Blood Pressure</option>
                        <option value="blood_sugar">Blood Sugar</option>
                        <option value="weight">Weight</option>
                        <option value="temperature">Temperature</option>
                        <option value="heart_rate">Heart Rate</option>
                        <option value="medication">Medication</option>
                        <option value="symptoms">Symptoms</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Value</label>
                    <input type="text" name="value" placeholder="Enter value" required>
                </div>
                <div class="form-group">
                    <label>Unit</label>
                    <input type="text" name="unit" placeholder="e.g., mg/dL, kg, °F">
                </div>
                <div class="form-group">
                    <label>Date & Time</label>
                    <input type="datetime-local" name="recorded_at" required>
                </div>
                <div class="form-group">
                    <label>Notes (Optional)</label>
                    <textarea name="notes" rows="3" placeholder="Additional notes..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeAddRecordModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>

    </div> <!-- End main-container -->
</div> <!-- End dashboard-wrapper -->

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>

