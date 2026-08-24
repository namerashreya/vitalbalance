<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

$user_id = $_SESSION['user_id'];

// Get user info
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Check health_records table structure
$hr_columns_check = mysqli_query($conn, "SHOW COLUMNS FROM health_records");
$hr_columns = [];
while ($col = mysqli_fetch_assoc($hr_columns_check)) {
    $hr_columns[] = $col['Field'];
}

// Get user's diseases for context
$diseases_query = "SELECT ud.*, d.disease_name FROM user_diseases ud 
                   JOIN diseases d ON ud.disease_id = d.disease_id 
                   WHERE ud.user_id = $user_id";
$diseases_result = mysqli_query($conn, $diseases_query);
$user_diseases = [];
while ($d = mysqli_fetch_assoc($diseases_result)) {
    $user_diseases[] = $d;
}

// Check health_records table structure for date column and primary key
$hr_columns_check = mysqli_query($conn, "SHOW COLUMNS FROM health_records");
$has_recorded_at = false;
$has_date_recorded = false;
$has_created_at = false;
$primary_key = null;
while ($col = mysqli_fetch_assoc($hr_columns_check)) {
    if ($col['Field'] === 'recorded_at') $has_recorded_at = true;
    if ($col['Field'] === 'date_recorded') $has_date_recorded = true;
    if ($col['Field'] === 'created_at') $has_created_at = true;
    if ($col['Key'] === 'PRI') {
        $primary_key = $col['Field'];
    }
}

// Determine which date column to use
$date_field = $primary_key; // fallback to primary key
if ($has_recorded_at) {
    $date_field = 'recorded_at';
} elseif ($has_date_recorded) {
    $date_field = 'date_recorded';
} elseif ($has_created_at) {
    $date_field = 'created_at';
}

// Get recent health records
if ($date_field) {
    $records_query = "SELECT * FROM health_records WHERE user_id = $user_id ORDER BY $date_field DESC LIMIT 50";
} else {
    // If no date field found, just get records without ordering
    $records_query = "SELECT * FROM health_records WHERE user_id = $user_id LIMIT 50";
}
$records_result = mysqli_query($conn, $records_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Health Records</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/vb-ux.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/vb-ux.js" defer></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-beige);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-beige);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, var(--secondary-sage-ultra-light) 0%, transparent 25%),
                radial-gradient(circle at 90% 80%, var(--primary-green-soft) 0%, transparent 25%);
        }

        .health-records-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
            position: relative;
            z-index: 1;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 16px;
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 36px;
            font-weight: 800;
            color: var(--text-primary);
        }
        
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 28px;
            color: var(--text-primary);
            box-shadow: var(--shadow-md);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
            border: 1px solid var(--primary-green-soft);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transition: all 0.6s ease;
        }

        .stat-card:hover::before {
            top: -60%;
            right: -60%;
        }
        
        .stat-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stat-card:nth-child(2) {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            color: white;
            animation-delay: 0.2s;
        }
        
        .stat-card:nth-child(3) {
            background: linear-gradient(135deg, var(--secondary-sage), var(--secondary-sage-dark));
            color: white;
            animation-delay: 0.3s;
        }
        
        .stat-card:nth-child(4) {
            background: linear-gradient(135deg, var(--primary-green-light) 0%, var(--primary-green) 100%);
            color: white;
            animation-delay: 0.4s;
        }
        
        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .stat-value {
            font-size: 42px;
            font-weight: 800;
            margin: 12px 0;
            font-family: 'Outfit', sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-label {
            font-size: 14px;
            opacity: 0.95;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            font-size: 32px;
            opacity: 0.3;
            position: absolute;
            right: 20px;
            top: 20px;
        }
        
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 24px;
            margin-bottom: 32px;
        }
        
        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .card:hover {
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .card-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            font-family: 'Outfit', sans-serif;
            color: var(--primary-green);
        }
        
        .quick-add-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            letter-spacing: 0.3px;
        }
        
        .form-group input,
        .form-group select {
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            background: white;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 4px var(--primary-green-soft);
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }
        
        .records-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .records-list::-webkit-scrollbar {
            width: 8px;
        }

        .records-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .records-list::-webkit-scrollbar-thumb {
            background: var(--primary-green);
            border-radius: 10px;
        }
        
        .record-item {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 20px;
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .record-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, var(--primary-green), var(--primary-green-dark));
            transition: width 0.3s ease;
        }
        
        .record-item:hover::before {
            width: 100%;
            opacity: 0.05;
        }

        .record-item:hover {
            background: white;
            transform: translateX(8px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        
        .record-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .record-type {
            font-weight: 700;
            color: #0f172a;
            font-size: 16px;
        }
        
        .record-date {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        
        .record-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-green);
            margin: 8px 0;
            font-family: 'Outfit', sans-serif;
        }
        
        .motivation-card {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
            border-radius: 24px;
            padding: 32px;
            color: white;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(45, 90, 39, 0.3);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 10px 30px rgba(45, 90, 39, 0.3);
            }
            50% {
                box-shadow: 0 15px 40px rgba(45, 90, 39, 0.5);
            }
        }

        .motivation-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .motivation-icon {
            font-size: 56px;
            margin-bottom: 16px;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .motivation-text {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
        }
        
        .motivation-subtext {
            font-size: 15px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }
        
        .achievement-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            padding: 10px 16px;
            border-radius: 25px;
            font-size: 13px;
            margin-top: 16px;
            margin-right: 8px;
            font-weight: 600;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .achievement-badge:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: scale(1.05);
        }
        
        .suggestion-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 5px solid #f59e0b;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .suggestion-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #f59e0b, #d97706);
            transition: width 0.3s ease;
        }

        .suggestion-card:hover::before {
            width: 100%;
            opacity: 0.05;
        }

        .suggestion-card:hover {
            transform: translateX(8px);
            box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
        }
        
        .suggestion-title {
            font-weight: 700;
            color: #92400e;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
        }
        
        .suggestion-text {
            font-size: 14px;
            color: #78350f;
            line-height: 1.6;
        }
        
        .chart-container {
            position: relative;
            height: 350px;
            margin-top: 24px;
            padding: 16px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
        }
        
        .weekly-updates {
            background: linear-gradient(135deg, var(--secondary-sage-ultra-light) 0%, var(--bg-light-green) 100%);
            border-radius: 16px;
            padding: 20px;
            margin-top: 16px;
        }
        
        .update-item {
            padding: 16px;
            background: white;
            border-radius: 12px;
            margin-bottom: 12px;
            border-left: 4px solid var(--primary-green);
            transition: all 0.3s ease;
        }

        .update-item:hover {
            transform: translateX(8px);
            box-shadow: var(--shadow-md);
        }
        
        .update-date {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
            font-weight: 600;
        }
        
        .update-text {
            font-size: 14px;
            color: #0f172a;
            line-height: 1.5;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 24px;
            color: #64748b;
        }
        
        .empty-state-icon {
            font-size: 72px;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        
        .filter-tab {
            padding: 10px 20px;
            border-radius: 25px;
            border: 2px solid #e2e8f0;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 600;
        }
        
        .filter-tab.active {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.3);
        }
        
        .filter-tab:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
        }
        
        .filter-tab.active:hover {
            background: linear-gradient(135deg, #5568d3, #6a3f8f);
        }

        /* Interactive Calendar Styles */
        .calendar-container {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-top: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .calendar-month {
            font-size: 22px;
            font-weight: 700;
            font-family: var(--font-heading);
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .calendar-nav {
            display: flex;
            gap: 12px;
        }

        .calendar-nav button {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            color: white;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .calendar-nav button:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-hover);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            color: #64748b;
            padding: 12px;
            font-size: 13px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            position: relative;
            background: #f8fafc;
        }

        .calendar-day:hover {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.3);
        }

        .calendar-day.has-record {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
            animation: recordPulse 2s ease-in-out infinite;
        }

        @keyframes recordPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(67, 233, 123, 0.7);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(67, 233, 123, 0);
            }
        }

        .calendar-day.today {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
            font-weight: 800;
        }

        .calendar-day.selected {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            color: white;
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.4);
        }

        .calendar-day.other-month {
            opacity: 0.3;
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Success Animation */
        @keyframes successPop {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-message {
            animation: successPop 0.5s ease-out;
        }
    </style>
</head>
<body>

    <?php include('../includes/header.php'); ?>

    <main class="health-records-container">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-file-medical" style="color: var(--primary-green);"></i> Health Records & Progress</h1>
                <p style="color: var(--text-secondary); margin: 8px 0 0 0; font-size: 16px;">Track your health journey and visualize your improvements</p>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats" id="quickStats">
            <!-- Will be populated by JavaScript -->
        </div>

        <!-- Main Content Grid -->
        <div class="main-grid">
            <!-- Left Column: Records & Charts -->
            <div>
                <!-- Quick Add Form -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-plus-circle" style="color: var(--primary-green);"></i> Quick Add Record</h2>
                    </div>
                    <form id="quickAddForm" class="quick-add-form">
                        <div class="form-group">
                            <label><i class="fas fa-clipboard-list"></i> Type</label>
                            <select name="record_type" id="record_type" required>
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
                            <label><i class="fas fa-chart-bar"></i> Value</label>
                            <input type="text" name="value" id="value" placeholder="e.g., 120/80" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-ruler"></i> Unit</label>
                            <input type="text" name="unit" id="unit" placeholder="e.g., mmHg">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar-days"></i> Date & Time</label>
                            <input type="datetime-local" name="recorded_at" id="recorded_at" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label><i class="fas fa-sticky-note"></i> Notes</label>
                            <input type="text" name="notes" id="notes" placeholder="Optional notes...">
                        </div>
                        <button type="submit" class="btn-primary" style="grid-column: 1 / -1;">
                            <span id="btnText">Add Record</span>
                        </button>
                    </form>
                </div>

                <!-- Interactive Calendar -->
                <div class="card calendar-container">
                    <div class="calendar-header">
                        <h2 class="calendar-month" id="calendarMonth">January 2026</h2>
                        <div class="calendar-nav">
                            <button type="button" id="prevMonth"><i class="fas fa-chevron-left"></i></button>
                            <button type="button" id="nextMonth"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="calendar-grid" id="calendarGrid">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Progress Chart -->
                <div class="card" style="margin-top: 24px;">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-chart-line" style="color: var(--primary-green);"></i> Progress Trend</h2>
                        <select id="chartType" style="padding: 10px 16px; border-radius: 10px; border: 2px solid #e2e8f0; font-weight: 600;">
                            <option value="blood_pressure">Blood Pressure</option>
                            <option value="blood_sugar">Blood Sugar</option>
                            <option value="weight">Weight</option>
                            <option value="heart_rate">Heart Rate</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="progressChart"></canvas>
                    </div>
                </div>

                <!-- Records List -->
                <div class="card" style="margin-top: 24px;">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-history" style="color: var(--primary-green);"></i> Recent Records</h2>
                    </div>
                    <div class="filter-tabs">
                        <div class="filter-tab active" data-filter="all">All</div>
                        <div class="filter-tab" data-filter="vitals"><i class="fas fa-heart-pulse"></i> Vitals</div>
                        <div class="filter-tab" data-filter="blood_pressure"><i class="fas fa-stethoscope"></i> BP</div>
                        <div class="filter-tab" data-filter="blood_sugar"><i class="fas fa-droplet"></i> Sugar</div>
                        <div class="filter-tab" data-filter="weight"><i class="fas fa-weight"></i> Weight</div>
                        <div class="filter-tab" data-filter="heart_rate"><i class="fas fa-heart"></i> Heart</div>
                    </div>
                    <div class="records-list" id="recordsList">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Right Column: Motivation & Suggestions -->
            <div>
                <!-- Motivation Card -->
                <div class="motivation-card" id="motivationCard">
                    <div class="motivation-icon"><i class="fas fa-bullseye"></i></div>
                    <div class="motivation-text" id="motivationText">Keep up the great work!</div>
                    <div class="motivation-subtext" id="motivationSubtext">You're making excellent progress</div>
                    <div id="achievements"></div>
                </div>

                <!-- Suggestions -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-lightbulb" style="color: #f59e0b;"></i> Smart Suggestions</h2>
                    </div>
                    <div id="suggestions">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Weekly Updates -->
                <div class="card" style="margin-top: 24px;">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-calendar-check" style="color: var(--primary-green);"></i> Weekly Updates</h2>
                    </div>
                    <div class="weekly-updates" id="weeklyUpdates">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Global variables
        let currentDate = new Date();
        let recordDates = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadQuickStats();
            loadRecords();
            loadProgressChart();
            loadMotivation();
            loadSuggestions();
            loadWeeklyUpdates();
            renderCalendar();
            
            // Set default datetime to now
            document.getElementById('recorded_at').value = new Date().toISOString().slice(0, 16);
        });

        // Quick Add Form
        document.getElementById('quickAddForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btnText = document.getElementById('btnText');
            
            btnText.innerHTML = '<span class="loading"></span>';
            
            fetch('../api/add_record.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Success animation
                    btnText.textContent = '✓ Added!';
                    btnText.parentElement.style.background = 'linear-gradient(135deg, #43e97b, #38f9d7)';
                    
                    setTimeout(() => {
                        btnText.textContent = 'Add Record';
                        btnText.parentElement.style.background = 'linear-gradient(135deg, #667eea, #764ba2)';
                    }, 2000);
                    
                    this.reset();
                    document.getElementById('recorded_at').value = new Date().toISOString().slice(0, 16);
                    loadQuickStats();
                    loadRecords();
                    loadProgressChart();
                    loadMotivation();
                    loadSuggestions();
                    renderCalendar();
                } else {
                    btnText.textContent = '✗ Error';
                    btnText.parentElement.style.background = 'linear-gradient(135deg, #f093fb, #f5576c)';
                    
                    setTimeout(() => {
                        btnText.textContent = 'Add Record';
                        btnText.parentElement.style.background = 'linear-gradient(135deg, #667eea, #764ba2)';
                    }, 2000);
                }
            })
            .catch(() => {
                btnText.textContent = 'Add Record';
            });
        });

        // Calendar Navigation
        document.getElementById('prevMonth').addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        // Render Calendar
        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];
            
            document.getElementById('calendarMonth').textContent = `${monthNames[month]} ${year}`;
            
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();
            
            let html = '';
            const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            dayHeaders.forEach(day => {
                html += `<div class="calendar-day-header">${day}</div>`;
            });
            
            // Previous month days
            for (let i = firstDay - 1; i >= 0; i--) {
                html += `<div class="calendar-day other-month">${daysInPrevMonth - i}</div>`;
            }
            
            // Current month days
            const today = new Date();
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const isToday = today.getDate() === day && today.getMonth() === month && today.getFullYear() === year;
                const hasRecord = recordDates.includes(dateStr);
                
                let classes = 'calendar-day';
                if (isToday) classes += ' today';
                if (hasRecord) classes += ' has-record';
                
                html += `<div class="${classes}" data-date="${dateStr}">${day}</div>`;
            }
            
            // Next month days
            const remainingDays = 42 - (firstDay + daysInMonth);
            for (let day = 1; day <= remainingDays; day++) {
                html += `<div class="calendar-day other-month">${day}</div>`;
            }
            
            document.getElementById('calendarGrid').innerHTML = html;
            
            // Add click handlers
            document.querySelectorAll('.calendar-day:not(.other-month)').forEach(day => {
                day.addEventListener('click', function() {
                    document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                    this.classList.add('selected');
                    const date = this.dataset.date;
                    if (date) {
                        loadRecordsByDate(date);
                    }
                });
            });
        }

        // Load records by date
        function loadRecordsByDate(date) {
            fetch(`../api/get_health_records.php?date=${date}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.records.length > 0) {
                        displayRecords(data.records);
                    } else {
                        document.getElementById('recordsList').innerHTML = `
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
                                <p>No records for ${date}</p>
                            </div>
                        `;
                    }
                });
        }

        // Load Quick Stats
        function loadQuickStats() {
            fetch('../api/get_health_stats.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const stats = data.stats;
                        document.getElementById('quickStats').innerHTML = `
                            <div class="stat-card">
                                <i class="fas fa-database stat-icon"></i>
                                <div class="stat-label">Total Records</div>
                                <div class="stat-value">${stats.total_records || 0}</div>
                            </div>
                            <div class="stat-card">
                                <i class="fas fa-calendar-week stat-icon"></i>
                                <div class="stat-label">This Week</div>
                                <div class="stat-value">${stats.this_week || 0}</div>
                            </div>
                            <div class="stat-card">
                                <i class="fas fa-fire stat-icon"></i>
                                <div class="stat-label">Streak Days</div>
                                <div class="stat-value">${stats.streak || 0}</div>
                            </div>
                            <div class="stat-card">
                                <i class="fas fa-chart-line stat-icon"></i>
                                <div class="stat-label">Improvement</div>
                                <div class="stat-value">${stats.improvement || 0}%</div>
                            </div>
                        `;
                    }
                })
                .catch(() => {
                    document.getElementById('quickStats').innerHTML = `
                        <div class="stat-card">
                            <i class="fas fa-database stat-icon"></i>
                            <div class="stat-label">Total Records</div>
                            <div class="stat-value">0</div>
                        </div>
                    `;
                });
        }

        // Load Records
        function loadRecords(filter = 'all') {
            fetch('../api/get_health_records.php?filter=' + filter)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.records.length > 0) {
                        displayRecords(data.records);
                        // Extract dates for calendar
                        recordDates = data.records.map(r => r.recorded_at.split(' ')[0]);
                        renderCalendar();
                    } else {
                        document.getElementById('recordsList').innerHTML = `
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="fas fa-chart-bar"></i></div>
                                <p>No records yet. Start tracking your health!</p>
                            </div>
                        `;
                    }
                });
        }

        function displayRecords(records) {
            let html = '';
            records.forEach(record => {
                const date = new Date(record.recorded_at);
                html += `
                    <div class="record-item">
                        <div class="record-header">
                            <span class="record-type">${formatRecordType(record.record_type)}</span>
                            <span class="record-date">${formatDate(date)}</span>
                        </div>
                        <div class="record-value">${record.value} ${record.unit || ''}</div>
                        ${record.notes ? '<div style="font-size: 13px; color: #64748b; margin-top: 8px;">' + record.notes + '</div>' : ''}
                    </div>
                `;
            });
            document.getElementById('recordsList').innerHTML = html;
        }

        // Load Progress Chart
        let progressChart = null;
        function loadProgressChart(type = 'blood_pressure') {
            fetch('../api/get_health_chart_data.php?type=' + type)
                .then(res => res.json())
                .then(data => {
                    const ctx = document.getElementById('progressChart').getContext('2d');
                    if (progressChart) progressChart.destroy();
                    
                    progressChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels || [],
                            datasets: [{
                                label: formatRecordType(type),
                                data: data.values || [],
                                borderColor: '#667eea',
                                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointRadius: 6,
                                pointHoverRadius: 8,
                                pointBackgroundColor: '#667eea',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    borderRadius: 8,
                                    titleFont: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 13
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            }
                        }
                    });
                })
                .catch(err => {
                    console.error('Chart error:', err);
                });
        }

        document.getElementById('chartType').addEventListener('change', function() {
            loadProgressChart(this.value);
        });

        // Load Motivation
        function loadMotivation() {
            fetch('../api/get_motivation.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('motivationText').textContent = data.message;
                        document.getElementById('motivationSubtext').textContent = data.subtext;
                        
                        if (data.achievements && data.achievements.length > 0) {
                            let html = '';
                            data.achievements.forEach(ach => {
                                html += `<div class="achievement-badge">${ach.icon} ${ach.text}</div>`;
                            });
                            document.getElementById('achievements').innerHTML = html;
                        }
                    }
                });
        }

        // Load Suggestions
        function loadSuggestions() {
            fetch('../api/get_health_suggestions.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.suggestions.length > 0) {
                        let html = '';
                        data.suggestions.forEach(suggestion => {
                            html += `
                                <div class="suggestion-card">
                                    <div class="suggestion-title">${suggestion.icon} ${suggestion.title}</div>
                                    <div class="suggestion-text">${suggestion.text}</div>
                                </div>
                            `;
                        });
                        document.getElementById('suggestions').innerHTML = html;
                    } else {
                        document.getElementById('suggestions').innerHTML = '<p style="color: #64748b; text-align: center; padding: 20px;">Keep tracking to get personalized suggestions!</p>';
                    }
                });
        }

        // Load Weekly Updates
        function loadWeeklyUpdates() {
            fetch('../api/get_weekly_updates.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.updates.length > 0) {
                        let html = '';
                        data.updates.forEach(update => {
                            html += `
                                <div class="update-item">
                                    <div class="update-date">${update.date}</div>
                                    <div class="update-text">${update.text}</div>
                                </div>
                            `;
                        });
                        document.getElementById('weeklyUpdates').innerHTML = html;
                    } else {
                        document.getElementById('weeklyUpdates').innerHTML = '<p style="color: #64748b; text-align: center; padding: 20px;">No updates this week. Keep tracking!</p>';
                    }
                });
        }

        // Filter Tabs
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                loadRecords(this.dataset.filter);
            });
        });

        // Helper Functions
        function formatRecordType(type) {
            const types = {
                'blood_pressure': '<i class="fas fa-stethoscope"></i> Blood Pressure',
                'blood_sugar': '<i class="fas fa-droplet"></i> Blood Sugar',
                'weight': '<i class="fas fa-weight"></i> Weight',
                'temperature': '<i class="fas fa-thermometer-half"></i> Temperature',
                'heart_rate': '<i class="fas fa-heart-pulse"></i> Heart Rate',
                'medication': '<i class="fas fa-pills"></i> Medication',
                'symptoms': '<i class="fas fa-head-side-cough"></i> Symptoms'
            };
            return types[type] || type;
        }

        function formatDate(date) {
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    </script>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
