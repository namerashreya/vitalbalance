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

// --- Data for User Growth (Last 6 Months) ---
$growth_data = [];
$growth_labels = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $display_month = date('M Y', strtotime("-$i months"));
    $res = mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'");
    $count = mysqli_fetch_row($res)[0];
    $growth_data[] = $count;
    $growth_labels[] = $display_month;
}

// --- Data for Top Diseases ---
$top_diseases_labels = [];
$top_diseases_counts = [];
$top_res = mysqli_query($conn, "SELECT d.disease_name, COUNT(ud.disease_id) as total 
                                FROM user_diseases ud 
                                JOIN diseases d ON ud.disease_id = d.disease_id 
                                GROUP BY ud.disease_id 
                                ORDER BY total DESC LIMIT 5");
while($row = mysqli_fetch_assoc($top_res)) {
    $top_diseases_labels[] = $row['disease_name'];
    $top_diseases_counts[] = $row['total'];
}

// --- Status Distribution ---
$status_labels = ['Active', 'Managed', 'Recovered'];
$status_counts = [];
foreach($status_labels as $status) {
    $s = strtolower($status);
    $res = mysqli_query($conn, "SELECT COUNT(*) FROM user_diseases WHERE status = '$s'");
    $status_counts[] = mysqli_fetch_row($res)[0];
}

// Handle Export
if(isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="vitalbalance_report_'.date('Ymd').'.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['User ID', 'Name', 'Email', 'Role', 'Status', 'Joined Date']);
    
    $users_res = mysqli_query($conn, "SELECT user_id, name, email, role, status, created_at FROM users ORDER BY created_at DESC");
    while($row = mysqli_fetch_assoc($users_res)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Statistics & Reports</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .report-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-top: 20px;
        }
        .chart-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            min-height: 400px;
        }
        .chart-card.full-width { grid-column: span 2; }
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .chart-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Shared Admin Header -->
    <?php include('../includes/admin_header.php'); ?>

    <main class="main-container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
            <div>
                <h1 class="page-title" style="margin-bottom: 0;">Statistics & Reports</h1>
                <p class="page-subtitle">Visual analytics and data insights for the VitalBalance platform</p>
            </div>
            <a href="reports.php?export=csv" class="btn btn-primary">
                <i class="fas fa-file-export"></i> Export All Users Data
            </a>
        </div>

        <div class="report-grid">
            <!-- User Growth -->
            <div class="chart-card full-width">
                <div class="chart-header">
                    <div class="chart-title"><i class="fas fa-chart-line"></i> User Registration Growth (Last 6 Months)</div>
                </div>
                <div class="chart-container">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>

            <!-- Top Diseases -->
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title"><i class="fas fa-virus"></i> Top 5 Chronic Conditions</div>
                </div>
                <div class="chart-container">
                    <canvas id="diseaseChart"></canvas>
                </div>
            </div>

            <!-- Condition Status -->
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title"><i class="fas fa-heartbeat"></i> Health Progress Distribution</div>
                </div>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Set default font
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';

        // 1. Growth Chart
        const growthCtx = document.getElementById('growthChart').getContext('2d');
        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($growth_labels); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode($growth_data); ?>,
                    borderColor: '#2D5A27',
                    backgroundColor: 'rgba(45, 90, 39, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2D5A27',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. Disease Chart
        const diseaseCtx = document.getElementById('diseaseChart').getContext('2d');
        new Chart(diseaseCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($top_diseases_labels); ?>,
                datasets: [{
                    label: 'Cases',
                    data: <?php echo json_encode($top_diseases_counts); ?>,
                    backgroundColor: [
                        '#2D5A27', '#4A8C44', '#8BA888', '#D4A373', '#E63946'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    y: { grid: { display: false } }
                }
            }
        });

        // 3. Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($status_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($status_counts); ?>,
                    backgroundColor: ['#ef4444', '#f59e0b', '#2D5A27'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true }
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>
