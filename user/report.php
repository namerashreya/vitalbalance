<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

$user_id = $_SESSION['user_id'];

// Get user information
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get disease info
$diseases_query = "SELECT ud.*, d.disease_name, d.category as disease_category, d.description as disease_desc
                  FROM user_diseases ud 
                  JOIN diseases d ON ud.disease_id = d.disease_id 
                  WHERE ud.user_id = $user_id";
$diseases_result = mysqli_query($conn, $diseases_query);
$diseases = [];
while($row = mysqli_fetch_assoc($diseases_result)) {
    $diseases[] = $row;
}

// Get health records summary
$records_query = "SELECT * FROM health_records WHERE user_id = $user_id ORDER BY recorded_at DESC";
$records_result = mysqli_query($conn, $records_query);
$records = [];
while($row = mysqli_fetch_assoc($records_result)) {
    $records[] = $row;
}

// Get medications
$meds_query = "SELECT * FROM medications WHERE user_id = $user_id ORDER BY created_at DESC";
$meds_result = mysqli_query($conn, $meds_query);
$medications = [];
if($meds_result) {
    while($row = mysqli_fetch_assoc($meds_result)) {
        $medications[] = $row;
    }
}

// Get wellness scores history (Check if table exists)
$wellness_history = [];
$table_check = @mysqli_query($conn, "SHOW TABLES LIKE 'wellness_scores'");
if($table_check && mysqli_num_rows($table_check) > 0) {
    $wellness_query = "SELECT * FROM wellness_scores WHERE user_id = $user_id ORDER BY date_recorded ASC";
    $wellness_result = @mysqli_query($conn, $wellness_query);
    if($wellness_result) {
        while($row = mysqli_fetch_assoc($wellness_result)) {
            $wellness_history[] = $row;
        }
    }
} else {
    // Graceful fallback: Use latest calculated score as a single data point if no history
    $current_score = calculateWellnessScore($conn, $user_id);
    $wellness_history[] = [
        'date_recorded' => date('Y-m-d'),
        'overall_score' => $current_score
    ];
}

// Function to calculate live score if table is missing
if (!function_exists('calculateWellnessScore')) {
    function calculateWellnessScore($conn, $user_id) {
        $score = 100;
        $records_res = @mysqli_query($conn, "SELECT record_type, value FROM health_records WHERE user_id = $user_id ORDER BY recorded_at DESC LIMIT 20");
        if (!$records_res || mysqli_num_rows($records_res) == 0) return 75;
        
        $active_diseases_res = @mysqli_query($conn, "SELECT COUNT(*) as count FROM user_diseases WHERE user_id = $user_id AND status = 'active'");
        $active_diseases = $active_diseases_res ? mysqli_fetch_assoc($active_diseases_res)['count'] : 0;
        $score -= ($active_diseases * 5);
        
        return max(0, min(100, round($score)));
    }
}

// Re-fetch for latest sorting (desc)
$wellness_history_desc = array_reverse($wellness_history);

// Calculate some progress stats
$total_records = count($records);
$total_diseases = count($diseases);
$managed_diseases = count(array_filter($diseases, function($d) { return in_array($d['status'], ['managed', 'recovered', 'resolved']); }));

// Get latest wellness stats
$latest_wellness = !empty($wellness_history_desc) ? $wellness_history_desc[0]['overall_score'] : 70;
$previous_wellness = count($wellness_history_desc) > 1 ? $wellness_history_desc[1]['overall_score'] : $latest_wellness;
$wellness_diff = $latest_wellness - $previous_wellness;

// Health Insights Logic
$insights = [];
if ($latest_wellness >= 80) {
    $insights[] = "Your overall wellness is excellent! Keep up the consistent tracking and healthy habits.";
} elseif ($latest_wellness >= 60) {
    $insights[] = "You're making steady progress. Focusing on consistent medication adherence could help boost your score further.";
} else {
    $insights[] = "There's room for improvement in your wellness score. Try logging your vitals more frequently to get better insights.";
}

if ($managed_diseases == $total_diseases && $total_diseases > 0) {
    $insights[] = "Impressive! All your tracked conditions are currently in a managed or recovered state.";
}

// Prepare chart data
$chart_labels = [];
$chart_data = [];
foreach($wellness_history as $w) {
    $chart_labels[] = date('M d', strtotime($w['date_recorded']));
    $chart_data[] = $w['overall_score'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Health Progress Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-report: #2E7D32;
            --primary-light: #f0fdf4;
            --accent-report: #166534;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #f1f5f9;
        }

        body {
            background-color: #f1f5f9;
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            padding: 40px 20px;
            -webkit-print-color-adjust: exact;
        }

        .report-paper {
            background: white;
            max-width: 1000px;
            margin: 0 auto;
            padding: 50px 60px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.06);
            border-radius: 24px;
            position: relative;
            overflow: hidden;
        }

        .report-paper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: linear-gradient(to right, var(--primary-report), #A5D6A7, var(--primary-report));
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 30px;
            margin-bottom: 40px;
        }

        .logo-area h1 {
            font-family: 'Outfit', sans-serif;
            color: var(--primary-report);
            margin: 0;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .logo-area p {
            margin: 5px 0 0;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .report-meta {
            text-align: right;
        }

        .report-meta h2 {
            font-size: 18px;
            color: var(--text-main);
            margin: 0;
            font-weight: 700;
        }

        .report-meta p {
            margin: 5px 0 0;
            color: var(--text-muted);
            font-size: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
            background: #f8fafc;
            padding: 25px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
        }

        .info-item label {
            display: block;
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .info-item span {
            font-weight: 600;
            color: var(--text-main);
            font-size: 15px;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-report);
            margin: 40px 0 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-title i {
            font-size: 20px;
            opacity: 0.8;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1.5px;
            background: linear-gradient(to right, #e2e8f0, transparent);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: var(--primary-light);
            padding: 30px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 25px;
            border: 1px solid rgba(46, 125, 50, 0.1);
        }

        .score-circle {
            width: 100px;
            height: 100px;
            background: white;
            border: 6px solid var(--primary-report);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(46, 125, 50, 0.1);
            flex-shrink: 0;
        }

        .score-circle .val {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-report);
            line-height: 1;
        }

        .score-circle .lbl {
            font-size: 9px;
            color: var(--accent-report);
            font-weight: 700;
            text-transform: uppercase;
        }

        .summary-text h3 {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 700;
        }

        .summary-text p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.5;
        }

        .insight-card {
            background: #fffbeb;
            padding: 25px;
            border-radius: 20px;
            border-left: 6px solid #f59e0b;
        }

        .insight-card h3 {
            color: #92400e;
            font-size: 16px;
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .insight-list {
            margin: 0;
            padding-left: 20px;
            color: #78350f;
            font-size: 14px;
            line-height: 1.6;
        }

        .chart-box {
            padding: 20px;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            height: 300px;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        .data-table th {
            text-align: left;
            padding: 15px;
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border-color);
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-active { background: #fee2e2; color: #b91c1c; }
        .badge-managed { background: #fef3c7; color: #b45309; }
        .badge-recovered { background: #dcfce7; color: #15803d; }
        .badge-resolved { background: #dcfce7; color: #15803d; }

        .footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid var(--border-color);
            text-align: center;
        }

        .disclaimer {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .actions {
            max-width: 1000px;
            margin: 30px auto;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        @media print {
            body { padding: 0; background: white; }
            .report-paper { box-shadow: none; max-width: 100%; border-radius: 0; padding: 40px; }
            .actions { display: none; }
            .chart-box { break-inside: avoid; }
        }
    </style>
</head>
<body>

    <div class="report-paper" id="report">
        <div class="header">
            <div class="logo-area">
                <h1>VitalBalance</h1>
                <p>Health Progress Report</p>
            </div>
            <div class="report-meta">
                <h2>Clinical Summary</h2>
                <p>Report Date: <?php echo date('F d, Y'); ?></p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <label>Patient Name</label>
                <span><?php echo htmlspecialchars($user['name'] ?? $user['username']); ?></span>
            </div>
            <div class="info-item">
                <label>Patient Email</label>
                <span><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div class="info-item">
                <label>Member ID</label>
                <span>#VB-<?php echo str_pad($user_id, 5, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="info-item">
                <label>Tracking Period</label>
                <span><?php echo !empty($wellness_history) ? date('M Y', strtotime($wellness_history[0]['date_recorded'])) : 'N/A'; ?> - Present</span>
            </div>
        </div>

        <div class="grid-2">
            <div class="summary-card">
                <div class="score-circle">
                    <span class="val"><?php echo $latest_wellness; ?>%</span>
                    <span class="lbl">Score</span>
                </div>
                <div class="summary-text">
                    <h3>Wellness Overview</h3>
                    <p>
                        Your current wellness level is <strong><?php echo $latest_wellness; ?>%</strong>. 
                        This reflects a <strong><?php echo abs($wellness_diff); ?>% <?php echo $wellness_diff >= 0 ? 'improvement' : 'decrease'; ?></strong> 
                        since your last assessment.
                    </p>
                </div>
            </div>

            <div class="insight-card">
                <h3><i class="fas fa-lightbulb"></i> Clinical Insights</h3>
                <ul class="insight-list">
                    <?php foreach($insights as $insight): ?>
                        <li><?php echo $insight; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <?php if(count($wellness_history) > 1 || (isset($wellness_history[0]) && count($wellness_history) == 1)): ?>
        <h2 class="section-title"><i class="fas fa-chart-line"></i> Wellness Score Trend</h2>
        <div class="chart-box">
            <canvas id="wellnessChart"></canvas>
        </div>
        <?php endif; ?>

        <h2 class="section-title"><i class="fas fa-shield-virus"></i> Chronic Condition Status</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Condition</th>
                    <th>Category</th>
                    <th>Managed Since</th>
                    <th>Current Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($diseases)): ?>
                    <tr><td colspan="4" style="text-align:center; color: var(--text-muted);">No conditions tracked in this period.</td></tr>
                <?php else: ?>
                    <?php foreach($diseases as $d): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($d['disease_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($d['disease_category']); ?></td>
                        <td><?php echo $d['diagnosed_date'] ? date('M d, Y', strtotime($d['diagnosed_date'])) : 'N/A'; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $d['status']; ?>">
                                <?php echo ucfirst($d['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h2 class="section-title"><i class="fas fa-pills"></i> Active Medications</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Medication Name</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Started On</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($medications)): ?>
                    <tr><td colspan="4" style="text-align:center; color: var(--text-muted);">No active medications recorded.</td></tr>
                <?php else: ?>
                    <?php foreach($medications as $m): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($m['medication_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($m['dosage']); ?></td>
                        <td><?php echo htmlspecialchars($m['frequency']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($m['start_date'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer">
            <div class="disclaimer">
                <strong>Medical Disclaimer:</strong> This automated progress report is based on user-submitted data via the VitalBalance platform. 
                It is designed for personal wellness tracking and educational purposes only. This document does NOT constitute a formal medical 
                diagnosis or clinical prescription. Please consult with your primary healthcare provider for professional medical evaluations.
            </div>
            <p style="font-size: 11px; color: var(--text-muted);">Generated by VitalBalance AI Engine v2.0 • &copy; 2026 VitalBalance Health. All rights reserved.</p>
        </div>
    </div>

    <div class="actions">
        <button class="btn btn-secondary" style="background:#e2e8f0; color:#475569; border:none; padding:12px 25px; border-radius:12px; font-weight:600; cursor:pointer;" onclick="window.close()">Close</button>
        <button class="btn btn-primary" style="background:var(--primary-report); color:white; border:none; padding:12px 25px; border-radius:12px; font-weight:700; cursor:pointer;" onclick="window.print()">
            <i class="fas fa-file-pdf" style="margin-right: 8px;"></i> Save as PDF / Print
        </button>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        <?php if(!empty($chart_labels)): ?>
        const ctx = document.getElementById('wellnessChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Wellness Score',
                    data: <?php echo json_encode($chart_data); ?>,
                    borderColor: '#2E7D32',
                    backgroundColor: 'rgba(46, 125, 50, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2E7D32',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { borderDash: [5, 5] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
