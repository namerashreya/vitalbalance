<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

// Check if user is admin
if($_SESSION['role'] != 'admin'){
    header("Location: ../user/dashboard.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Get current admin information
$admin_query = "SELECT * FROM users WHERE user_id = $admin_id";
$admin_result = mysqli_query($conn, $admin_query);
$admin = mysqli_fetch_assoc($admin_result);

// Build search query
$where_clauses = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clauses[] = "(u.name LIKE '%$search%' OR u.email LIKE '%$search%' OR hr.record_type LIKE '%$search%')";
}
if (isset($_GET['record_type']) && !empty($_GET['record_type'])) {
    $type = mysqli_real_escape_string($conn, $_GET['record_type']);
    $where_clauses[] = "hr.record_type = '$type'";
}

$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = " WHERE " . implode(" AND ", $where_clauses);
}

// Get health records with user details
$records_query = "SELECT hr.*, u.name as user_name, u.email as user_email 
                  FROM health_records hr 
                  JOIN users u ON hr.user_id = u.user_id 
                  $where_sql 
                  ORDER BY hr.log_date DESC, hr.log_id DESC 
                  LIMIT 100";
$records_result = mysqli_query($conn, $records_query);

// Get counts for summary cards
$total_logs = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM health_records"))[0];
$today_logs = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM health_records WHERE DATE(recorded_at) = CURDATE()"))[0];
$high_pain = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM health_records WHERE pain_level >= 7"))[0];

// Get distinct record types for filter
$types_res = mysqli_query($conn, "SELECT DISTINCT record_type FROM health_records WHERE record_type IS NOT NULL AND record_type != ''");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Health Records Management</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .records-table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-top: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-mini-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .stat-mini-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .icon-blue { background: #eff6ff; color: #3b82f6; }
        .icon-green { background: #f0fdf4; color: #10b981; }
        .icon-red { background: #fef2f2; color: #ef4444; }

        .stat-mini-info h4 { font-size: 13px; color: #64748b; margin: 0; }
        .stat-mini-info p { font-size: 20px; font-weight: 700; color: #1e293b; margin: 4px 0 0 0; }

        .search-flex {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .search-flex input, .search-flex select {
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }
        .badge-type {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background: var(--bg-beige);
            color: var(--text-secondary);
            text-transform: capitalize;
            border: 1px solid var(--secondary-sage-light);
        }
        .pain-badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
        }
        .pain-low { background: #dcfce7; color: #166534; }
        .pain-med { background: #fef9c3; color: #854d0e; }
        .pain-high { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <!-- Shared Admin Header -->
    <?php include('../includes/admin_header.php'); ?>

    <main class="main-container">
        <h1 class="page-title">Health Records Management</h1>
        <p class="page-subtitle">Monitor and review all health activity logs submitted by platform users</p>

        <div class="stats-grid">
            <div class="stat-mini-card">
                <div class="stat-mini-icon icon-blue"><i class="fas fa-file-medical"></i></div>
                <div class="stat-mini-info">
                    <h4>Total Logs</h4>
                    <p><?php echo number_format($total_logs); ?></p>
                </div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-mini-icon icon-green"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-mini-info">
                    <h4>Recorded Today</h4>
                    <p><?php echo number_format($today_logs); ?></p>
                </div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-mini-icon icon-red"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-mini-info">
                    <h4>Critical Pain (7+)</h4>
                    <p><?php echo number_format($high_pain); ?></p>
                </div>
            </div>
        </div>

        <div class="records-table-card">
            <form action="health_records.php" method="GET" class="search-flex">
                <input type="text" name="search" placeholder="Search user or record type..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="flex: 2;">
                <select name="record_type" style="flex: 1;">
                    <option value="">All Record Types</option>
                    <?php while($t = mysqli_fetch_assoc($types_res)): ?>
                        <option value="<?php echo htmlspecialchars($t['record_type']); ?>" <?php echo isset($_GET['record_type']) && $_GET['record_type'] == $t['record_type'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($t['record_type']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <?php if(isset($_GET['search']) || isset($_GET['record_type'])): ?>
                    <a href="health_records.php" class="btn btn-secondary">Reset</a>
                <?php endif; ?>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Date</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Vitals / Reading</th>
                        <th>Pain</th>
                        <th>Activity</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($records_result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($records_result)): ?>
                            <tr>
                                <td style="font-size: 13px; color: #64748b;">
                                    <?php echo date('M d, Y', strtotime($row['log_date'])); ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($row['user_name']); ?></div>
                                    <div style="font-size: 11px; color: #94a3b8;"><?php echo htmlspecialchars($row['user_email']); ?></div>
                                </td>
                                <td><span class="badge-type"><?php echo htmlspecialchars($row['record_type']); ?></span></td>
                                <td style="font-size: 13px;">
                                    <?php 
                                        $readings = [];
                                        if($row['blood_sugar']) $readings[] = "Sugar: " . $row['blood_sugar'];
                                        if($row['blood_pressure']) $readings[] = "BP: " . $row['blood_pressure'];
                                        if($row['sleep_hours']) $readings[] = "Sleep: " . $row['sleep_hours'] . "h";
                                        if($row['value']) $readings[] = $row['value'] . " " . $row['unit'];
                                        
                                        echo !empty($readings) ? implode(" | ", $readings) : "-";
                                    ?>
                                </td>
                                <td>
                                    <?php if($row['pain_level'] !== null): ?>
                                        <?php 
                                            $p = $row['pain_level'];
                                            $p_class = $p < 4 ? 'pain-low' : ($p < 7 ? 'pain-med' : 'pain-high');
                                        ?>
                                        <span class="pain-badge <?php echo $p_class; ?>">Level <?php echo $p; ?></span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 13px; color: #64748b;">
                                    <?php echo htmlspecialchars($row['activity_level'] ?: '-'); ?>
                                </td>
                                <td style="font-size: 12px; color: #94a3b8; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($row['notes']); ?>">
                                    <?php echo htmlspecialchars($row['notes'] ?: 'No notes'); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                                <i class="fas fa-notes-medical" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
                                No health records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
