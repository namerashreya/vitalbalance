<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

$user_id = $_SESSION['user_id'];

// Get user info (for header)
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get user's diseases - check what columns exist first
$columns_check = mysqli_query($conn, "SHOW COLUMNS FROM diseases");
$disease_columns = [];
while ($col = mysqli_fetch_assoc($columns_check)) {
    $disease_columns[] = $col['Field'];
}

// Build SELECT dynamically based on available columns
$select_fields = "ud.*, d.disease_name";
if (in_array('description', $disease_columns)) {
    $select_fields .= ", d.description";
}
if (in_array('icon', $disease_columns)) {
    $select_fields .= ", d.icon";
}
if (in_array('color_code', $disease_columns)) {
    $select_fields .= ", d.color_code";
}

// Check user_diseases table structure for primary key and available columns
$ud_columns_check = mysqli_query($conn, "SHOW COLUMNS FROM user_diseases");
$ud_primary_key = 'id'; // default
$ud_columns = [];
$has_created_at = false;
while ($col = mysqli_fetch_assoc($ud_columns_check)) {
    $ud_columns[] = $col['Field'];
    if ($col['Key'] === 'PRI') {
        $ud_primary_key = $col['Field'];
    }
    if ($col['Field'] === 'created_at') {
        $has_created_at = true;
    }
}

// Build ORDER BY clause based on available columns
$order_by = "d.disease_name ASC"; // Default order by disease name
if ($has_created_at) {
    $order_by = "ud.created_at DESC";
}

// Get user's diseases
$user_diseases_query = "SELECT $select_fields 
                       FROM user_diseases ud 
                       JOIN diseases d ON ud.disease_id = d.disease_id 
                       WHERE ud.user_id = $user_id 
                       ORDER BY $order_by";
$user_diseases_result = mysqli_query($conn, $user_diseases_query);

// Check for query errors
if (!$user_diseases_result) {
    $query_error = mysqli_error($conn);
    error_log("My Diseases Query Error: " . $query_error);
    $user_diseases_result = false; // Set to false so empty state shows
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | My Chronic Diseases</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header-inline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .page-header-inline h1 {
            font-size: 24px;
            margin: 0;
        }
        .chips-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }
        .chip {
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .chip:hover {
            background: #e2e8f0;
            transform: scale(1.05);
        }
        .chip.active {
            background: var(--primary-green);
            color: white;
            border-color: transparent;
            box-shadow: var(--shadow-sm);
        }
        .chip.active:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
        }
        .diseases-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px;
        }
        .disease-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.05);
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .disease-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-green);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .disease-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
            border-color: #cbd5e1;
        }
        .disease-card:hover::before {
            opacity: 1;
        }
        .disease-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        .disease-main {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .disease-icon-pill {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        .disease-card:hover .disease-icon-pill {
            transform: scale(1.1) rotate(5deg);
        }
        .disease-name {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-active {
            background: #fee2e2;
            color: #b91c1c;
        }
        .status-managed {
            background: #dcfce7;
            color: #166534;
        }
        .status-recovered {
            background: #fef3c7;
            color: #92400e;
        }
        .status-select {
            appearance: none;
            -webkit-appearance: none;
            border: none;
            padding: 4px 10px;
            padding-right: 20px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            outline: none;
            text-align: center;
            background-repeat: no-repeat;
            background-position: right 6px center;
            background-size: 8px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3F%3E%3C/svg%3E");
        }
        .disease-meta {
            font-size: 12px;
            color: #64748b;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .dot {
            width: 3px;
            height: 3px;
            border-radius: 999px;
            background: #cbd5e1;
        }
        .disease-notes {
            font-size: 13px;
            color: #0f172a;
        }
        .card-footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }
        .card-footer-row small {
            font-size: 11px;
            color: #94a3b8;
        }
        .btn-pill {
            border-radius: 999px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .empty-state-center {
            text-align: center;
            padding: 48px 16px;
        }
        .empty-state-center .icon-circle {
            width: 64px;
            height: 64px;
            border-radius: 999px;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: var(--primary-green);
            margin: 0 auto 16px;
        }
        .empty-state-center h2 {
            margin-bottom: 6px;
        }
        .empty-state-center p {
            margin-bottom: 16px;
            color: #64748b;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Shared Header & Nav -->
    <?php include('../includes/header.php'); ?>


    <main class="main-container" style="max-width: 1400px; margin: 0 auto; width: 100%; padding: 2rem;">
        <div class="page-header-inline">
            <div>
                <h1><i class="fas fa-notes-medical" style="color: var(--primary-green);"></i> My Chronic Diseases</h1>
                <p class="page-subtitle">A centralized view of all conditions you’re tracking in VitalBalance.</p>
            </div>
            <a href="add_disease.php" class="btn btn-primary">
                + Add Disease
            </a>
        </div>

        <div class="chips-row">
            <div class="chip active" data-filter="all">All</div>
            <div class="chip" data-filter="active">Active</div>
            <div class="chip" data-filter="managed">Managed</div>
            <div class="chip" data-filter="recovered">Recovered</div>
        </div>

        <?php if($user_diseases_result && mysqli_num_rows($user_diseases_result) > 0): ?>
            <div class="diseases-grid" id="diseasesGrid">
                <?php 
                while($d = mysqli_fetch_assoc($user_diseases_result)):
                    // Handle status - check if it exists, default to 'active'
                    $status = isset($d['status']) ? $d['status'] : 'active';
                    $status_class = $status === 'active' ? 'status-active'
                                   : ($status === 'managed' ? 'status-managed' : 'status-recovered');
                    
                    // Get the primary key value
                    $disease_id_value = isset($d['id']) ? $d['id'] : (isset($d['user_disease_id']) ? $d['user_disease_id'] : 0);

                    // Determine icon based on name
                    $icon_class = 'fa-heart-pulse'; 
                    $name_lower = strtolower($d['disease_name'] ?? '');
                    
                    if (strpos($name_lower, 'diabetes') !== false) $icon_class = 'fa-droplet';
                    elseif (strpos($name_lower, 'blood') !== false && strpos($name_lower, 'pressure') !== false) $icon_class = 'fa-heart-pulse';
                    elseif (strpos($name_lower, 'heart') !== false) $icon_class = 'fa-heart';
                    elseif (strpos($name_lower, 'asthma') !== false || strpos($name_lower, 'lung') !== false || strpos($name_lower, 'breath') !== false) $icon_class = 'fa-lungs';
                    elseif (strpos($name_lower, 'anxiety') !== false || strpos($name_lower, 'depress') !== false || strpos($name_lower, 'mental') !== false) $icon_class = 'fa-brain';
                    elseif (strpos($name_lower, 'sleep') !== false || strpos($name_lower, 'apnea') !== false) $icon_class = 'fa-bed';
                    elseif (strpos($name_lower, 'alzheimer') !== false) $icon_class = 'fa-head-side-virus';
                    elseif (strpos($name_lower, 'pcod') !== false || strpos($name_lower, 'gyno') !== false) $icon_class = 'fa-venus';
                    elseif (strpos($name_lower, 'arthri') !== false || strpos($name_lower, 'joint') !== false || strpos($name_lower, 'bone') !== false) $icon_class = 'fa-bone';
                    elseif (strpos($name_lower, 'migraine') !== false || strpos($name_lower, 'headache') !== false) $icon_class = 'fa-bolt';
                ?>
                <div class="disease-card" data-status="<?php echo $status; ?>" data-id="<?php echo $disease_id_value; ?>">
                    <div class="disease-card-header">
                        <div class="disease-main">
                            <div class="disease-icon-pill" style="background: <?php echo $d['color_code'] ?? '#8b5cf6'; ?>20; color: <?php echo $d['color_code'] ?? '#8b5cf6'; ?>;">
                                <i class="fas <?php echo $icon_class; ?>"></i>
                            </div>
                            <div>
                                <h3 class="disease-name"><?php echo htmlspecialchars($d['disease_name'] ?? 'Unknown Disease'); ?></h3>
                                <div class="disease-meta">
                                    <?php if(!empty($d['diagnosed_date'])): ?>
                                        <span><strong>Diagnosed:</strong> <?php echo date('M d, Y', strtotime($d['diagnosed_date'])); ?></span>
                                        <span class="dot"></span>
                                    <?php endif; ?>
                                    <?php if(!empty($d['created_at'])): ?>
                                        <span><strong>Added:</strong> <?php echo date('M Y', strtotime($d['created_at'])); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <select class="status-select <?php echo $status_class; ?>" 
                                onchange="updateStatus(this, <?php echo $disease_id_value; ?>)"
                                onclick="event.stopPropagation();">
                            <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="managed" <?php echo $status === 'managed' ? 'selected' : ''; ?>>Managed</option>
                            <option value="recovered" <?php echo $status === 'recovered' ? 'selected' : ''; ?>>Recovered</option>
                        </select>
                    </div>
                    <?php if(!empty($d['notes'])): ?>
                        <p class="disease-notes"><?php echo nl2br(htmlspecialchars($d['notes'])); ?></p>
                    <?php elseif(!empty($d['description'])): ?>
                        <p class="disease-notes" style="color:#64748b;"><?php echo htmlspecialchars(substr($d['description'], 0, 140)); ?><?php echo strlen($d['description']) > 140 ? '…' : ''; ?></p>
                    <?php endif; ?>
                    <div class="card-footer-row">
                        <small>ID: <?php echo $disease_id_value; ?></small>
                        <div style="display:flex;gap:6px;">
                            <button class="btn btn-secondary btn-pill" onclick="confirmRemove(<?php echo $disease_id_value; ?>)">Remove</button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state-center">
                <div class="icon-circle"><i class="fas fa-stethoscope"></i></div>                <h2>No chronic diseases recorded</h2>
                <p>Once you add a disease, it will appear here and on your main dashboard.</p>
                <a href="add_disease.php" class="btn btn-primary">Add Your First Disease</a>
            </div>
        <?php endif; ?>
    </main>

    <script>
        // Filter chips with smooth animations
        document.querySelectorAll('.chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                document.querySelectorAll('.chip').forEach(function(c) { c.classList.remove('active'); });
                this.classList.add('active');

                var filter = this.getAttribute('data-filter');
                var cards = document.querySelectorAll('.disease-card');
                var visibleCount = 0;
                
                cards.forEach(function(card) {
                    var shouldShow = filter === 'all' || card.getAttribute('data-status') === filter;
                    if (shouldShow) {
                        card.style.display = '';
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(10px)';
                        setTimeout(function() {
                            card.style.transition = 'all 0.3s ease';
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, visibleCount * 50);
                        visibleCount++;
                    } else {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(-10px)';
                        setTimeout(function() {
                            card.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });

        function updateStatus(select, id) {
            const newStatus = select.value;
            const card = select.closest('.disease-card');
            
            // Optimistic update of colors
            select.className = 'status-select';
            if (newStatus === 'active') select.classList.add('status-active');
            else if (newStatus === 'managed') select.classList.add('status-managed');
            else if (newStatus === 'recovered') select.classList.add('status-recovered');
            
            // Update data attribute for filtering
            card.setAttribute('data-status', newStatus);

            fetch('../api/update_disease_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    user_disease_id: id, 
                    status: newStatus 
                })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert('Failed to update status: ' + (data.message || 'Unknown error'));
                    // Revert (reload page or simplistic revert)
                    location.reload(); 
                }
            })
            .catch(err => {
                console.error(err);
                alert('Connection error');
            });
        }

        function confirmRemove(id) {
            if (!confirm('Remove this disease from your list?')) return;

            var card = document.querySelector('.disease-card[data-id="' + id + '"]');
            if (!card) return;

            // Animate removal
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';
            
            fetch('../api/delete_user_disease.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_disease_id: id })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.success) {
                    // Restore card on error
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                    alert(data.message || 'Failed to remove disease.');
                    return;
                }
                
                setTimeout(function() {
                    card.remove();
                    // If no more cards, show empty state
                    if (document.querySelectorAll('.disease-card').length === 0) {
                        location.reload();
                    }
                }, 300);
            })
            .catch(function() {
                // Restore card on error
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
                alert('Something went wrong. Please try again.');
            });
        }
        
        // Add fade-in animation on page load
        document.addEventListener('DOMContentLoaded', function() {
            var cards = document.querySelectorAll('.disease-card');
            cards.forEach(function(card, index) {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(function() {
                    card.style.transition = 'all 0.4s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
    <!-- Shared Footer -->
    <?php include('../includes/footer.php'); ?>
</body>
</html>


