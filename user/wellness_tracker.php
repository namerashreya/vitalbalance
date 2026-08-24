<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

$user_id = $_SESSION['user_id'];

// Get user info
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get user's wellness items (medications and natural remedies)
$meds_query = "SELECT * FROM medications WHERE user_id = $user_id ORDER BY created_at DESC";
$meds_result = mysqli_query($conn, $meds_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Wellness Tracker</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .header, .nav {
            position: relative;
            z-index: 100;
        }

        .floating-pill {
            position: absolute;
            border-radius: 50px;
            opacity: 0.1;
            animation: float 25s infinite ease-in-out;
        }

        .pill-1 {
            width: 200px;
            height: 80px;
            background: radial-gradient(circle, var(--secondary-sage), transparent 70%);
            top: 15%;
            left: 10%;
            animation-delay: 0s;
        }

        .pill-2 {
            width: 150px;
            height: 60px;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            top: 60%;
            right: 15%;
            animation-delay: 5s;
        }

        .pill-3 {
            width: 180px;
            height: 70px;
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            bottom: 20%;
            left: 25%;
            animation-delay: 10s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            25% {
                transform: translate(30px, 30px) rotate(90deg);
            }
            50% {
                transform: translate(0, 60px) rotate(180deg);
            }
            75% {
                transform: translate(-30px, 30px) rotate(270deg);
            }
        }

        .medication-container {
            width: 100%;
            margin: 0;
            padding: 24px;
            position: relative;
            z-index: 1;
        }

        .page-header {
            margin-bottom: 32px;
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
            margin-bottom: 8px;
        }

        .page-header p {
            color: var(--text-secondary);
            font-size: 16px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--bg-white);
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

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
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
            background: linear-gradient(135deg, var(--primary-green-soft), var(--secondary-sage-light));
            color: var(--text-primary);
            animation-delay: 0.4s;
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .stat-icon {
            font-size: 32px;
            opacity: 0.3;
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .stat-value {
            font-size: 42px;
            font-weight: 800;
            margin: 12px 0;
            font-family: 'Outfit', sans-serif;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.95;
            font-weight: 500;
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
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .card:hover {
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .card-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 24px;
            font-family: 'Outfit', sans-serif;
            color: var(--primary-green);
        }

        .add-med-form {
            display: grid;
            gap: 16px;
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
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            background: white;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .btn-primary {
            background: var(--primary-green);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4);
        }

        .medications-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .medications-list::-webkit-scrollbar {
            width: 8px;
        }

        .medications-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .medications-list::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
        }

        .med-item {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 20px;
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
            position: relative;
        }

        .med-item:hover {
            background: white;
            transform: translateX(8px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .med-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .med-name {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            font-family: 'Outfit', sans-serif;
        }

        .med-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }

        .status-completed {
            background: #e2e8f0;
            color: #64748b;
        }

        .status-stopped {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }

        .med-details {
            display: grid;
            gap: 8px;
            margin-top: 12px;
        }

        .med-detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #64748b;
        }

        .med-detail-item i {
            color: #667eea;
            width: 20px;
        }

        .med-actions {
            display: flex;
            gap: 8px;
            margin-top: 16px;
        }

        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .medication-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .btn-edit {
            background: var(--primary-green);
            color: white;
        }

        .btn-edit:hover {
            background: var(--primary-green-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.2);
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .btn-complete {
            background: #f0fdf4;
            color: #22c55e;
        }

        .btn-complete:hover {
            background: #22c55e;
            color: white;
        }

        .reminder-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .reminder-title {
            font-size: 18px;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .reminder-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .reminder-item {
            background: white;
            padding: 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .reminder-item:hover {
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }

        .reminder-time {
            font-size: 24px;
            font-weight: 700;
            color: #f59e0b;
            font-family: 'Outfit', sans-serif;
        }

        .reminder-med {
            flex: 1;
        }

        .reminder-med-name {
            font-weight: 600;
            color: #0f172a;
        }

        .reminder-med-dose {
            font-size: 13px;
            color: #64748b;
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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            padding: 32px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Shared Header & Nav -->
    <?php include('../includes/header.php'); ?>

    <main class="medication-container">
        <div class="page-header">
            <h1><i class="fas fa-leaf" style="color: var(--primary-green);"></i> Wellness Tracker</h1>
            <p>Manage your medications, natural remedies, and wellness routine</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <i class="fas fa-pills stat-icon"></i>
                <div class="stat-label">Active Medications</div>
                <div class="stat-value" id="activeMeds">0</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar-check stat-icon"></i>
                <div class="stat-label">Doses Today</div>
                <div class="stat-value" id="dosesToday">0</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock stat-icon"></i>
                <div class="stat-label">Next Dose In</div>
                <div class="stat-value" id="nextDose">--</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle stat-icon"></i>
                <div class="stat-label">Adherence Rate</div>
                <div class="stat-value" id="adherence">100%</div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="main-grid">
            <!-- Left Column: Add & List -->
            <div>
                <!-- Add Medication Form -->
                <div class="card">
                    <h2 class="card-title"><i class="fas fa-plus-circle" style="color: var(--primary-green);"></i> Add New Medication</h2>
                    <form id="addMedForm" class="add-med-form">
                        <div class="form-group">
                            <label><i class="fas fa-pills"></i> Medication Name</label>
                            <input type="text" name="medication_name" placeholder="e.g., Aspirin" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fas fa-prescription-bottle"></i> Dosage</label>
                                <input type="text" name="dosage" placeholder="e.g., 500mg" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-clock"></i> Frequency</label>
                                <select name="frequency" required>
                                    <option value="">Select...</option>
                                    <option value="Once daily">Once daily</option>
                                    <option value="Twice daily">Twice daily</option>
                                    <option value="Three times daily">Three times daily</option>
                                    <option value="Four times daily">Four times daily</option>
                                    <option value="Every 4 hours">Every 4 hours</option>
                                    <option value="Every 6 hours">Every 6 hours</option>
                                    <option value="Every 8 hours">Every 8 hours</option>
                                    <option value="Every 12 hours">Every 12 hours</option>
                                    <option value="As needed">As needed</option>
                                    <option value="Weekly">Weekly</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-alt"></i> Start Date</label>
                                <input type="date" name="start_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-calendar-times"></i> End Date (Optional)</label>
                                <input type="date" name="end_date">
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user-md"></i> Prescribed By</label>
                            <input type="text" name="prescribed_by" placeholder="Doctor's name">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-leaf"></i> Natural Remedies (Optional)</label>
                            <textarea name="natural_remedies" placeholder="e.g., Ginger tea, Turmeric, Meditation, etc."></textarea>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-notes-medical"></i> Notes</label>
                            <textarea name="notes" placeholder="Special instructions, side effects to watch for, etc."></textarea>
                        </div>

                        <button type="submit" class="btn-primary">
                            <i class="fas fa-plus"></i>
                            <span id="btnText">Add Medication</span>
                        </button>
                    </form>
                </div>

                <!-- Medications List -->
                <div class="card" style="margin-top: 24px;">
                    <h2 class="card-title"><i class="fas fa-notes-medical" style="color: var(--primary-green);"></i> My Medications</h2>
                    <div class="medications-list" id="medicationsList">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Right Column: Reminders & Tips -->
            <div>
                <!-- Today's Reminders -->
                <div class="reminder-card">
                    <div class="reminder-title">
                        <i class="fas fa-bell"></i>
                        Today's Medication Schedule
                    </div>
                    <div class="reminder-list" id="remindersList">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Medication Tips -->
                <div class="card">
                    <h2 class="card-title"><i class="fas fa-lightbulb" style="color: #f59e0b;"></i> Medication Tips</h2>
                    <div id="medicationTips">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <h2 class="card-title"><i class="fas fa-edit" style="color: var(--primary-green);"></i> Edit Medication</h2>
            <form id="editMedForm" class="add-med-form">
                <input type="hidden" name="medication_id" id="edit_medication_id">
                <!-- Same fields as add form -->
                <div class="form-group">
                    <label><i class="fas fa-pills"></i> Medication Name</label>
                    <input type="text" name="medication_name" id="edit_medication_name" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-prescription-bottle"></i> Dosage</label>
                        <input type="text" name="dosage" id="edit_dosage" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Frequency</label>
                        <select name="frequency" id="edit_frequency" required>
                            <option value="Once daily">Once daily</option>
                            <option value="Twice daily">Twice daily</option>
                            <option value="Three times daily">Three times daily</option>
                            <option value="As needed">As needed</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-notes-medical"></i> Notes</label>
                    <textarea name="notes" id="edit_notes"></textarea>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button type="button" class="btn-action btn-delete" onclick="closeEditModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadMedications();
            loadStats();
            loadReminders();
            loadTips();
        });

        // Add Medication
        document.getElementById('addMedForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btnText = document.getElementById('btnText');
            
            btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            
            fetch('../api/add_medication.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btnText.innerHTML = '<i class="fas fa-check"></i> Added!';
                    this.reset();
                    this.querySelector('[name="start_date"]').value = new Date().toISOString().split('T')[0];
                    
                    setTimeout(() => {
                        btnText.innerHTML = '<i class="fas fa-plus"></i> Add Medication';
                    }, 2000);
                    
                    loadMedications();
                    loadStats();
                    loadReminders();
                } else {
                    alert('Error: ' + data.message);
                    btnText.innerHTML = '<i class="fas fa-plus"></i> Add Medication';
                }
            });
        });

        // Load Medications
        function loadMedications() {
            fetch('../api/get_medications.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.medications.length > 0) {
                        displayMedications(data.medications);
                    } else {
                        document.getElementById('medicationsList').innerHTML = `
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="fas fa-pills" style="font-size: 3rem; color: var(--primary-green-soft);"></i></div>
                                <p>No medications added yet. Start tracking your medications!</p>
                            </div>
                        `;
                    }
                });
        }

        function displayMedications(medications) {
            let html = '';
            medications.forEach(med => {
                const statusClass = med.status === 'active' ? 'status-active' : 
                                  med.status === 'completed' ? 'status-completed' : 'status-stopped';
                
                html += `
                    <div class="med-item">
                        <div class="med-header">
                            <div class="med-name">${med.medication_name}</div>
                            <div class="med-status ${statusClass}">${med.status.toUpperCase()}</div>
                        </div>
                        <div class="med-details">
                            <div class="med-detail-item">
                                <i class="fas fa-prescription-bottle"></i>
                                <span>${med.dosage} - ${med.frequency}</span>
                            </div>
                            ${med.prescribed_by ? `
                                <div class="med-detail-item">
                                    <i class="fas fa-user-md"></i>
                                    <span>Prescribed by ${med.prescribed_by}</span>
                                </div>
                            ` : ''}
                            <div class="med-detail-item">
                                <i class="fas fa-calendar"></i>
                                <span>Started: ${formatDate(med.start_date)}</span>
                            </div>
                            ${med.notes ? `
                                <div class="med-detail-item">
                                    <i class="fas fa-notes-medical"></i>
                                    <span>${med.notes}</span>
                                </div>
                            ` : ''}
                        </div>
                        <div class="med-actions">
                            <button class="btn-action btn-edit" onclick="editMedication(${med.medication_id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            ${med.status === 'active' ? `
                                <button class="btn-action btn-complete" onclick="completeMedication(${med.medication_id})">
                                    <i class="fas fa-check"></i> Mark Complete
                                </button>
                            ` : ''}
                            <button class="btn-action btn-delete" onclick="deleteMedication(${med.medication_id})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                `;
            });
            document.getElementById('medicationsList').innerHTML = html;
        }

        // Load Stats
        function loadStats() {
            fetch('../api/get_medication_stats.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('activeMeds').textContent = data.stats.active_count || 0;
                        document.getElementById('dosesToday').textContent = data.stats.doses_today || 0;
                        document.getElementById('nextDose').textContent = data.stats.next_dose || '--';
                        document.getElementById('adherence').textContent = (data.stats.adherence || 100) + '%';
                    }
                });
        }

        // Load Reminders
        function loadReminders() {
            fetch('../api/get_medication_reminders.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.reminders.length > 0) {
                        let html = '';
                        data.reminders.forEach(reminder => {
                            html += `
                                <div class="reminder-item">
                                    <div class="reminder-time">${reminder.time}</div>
                                    <div class="reminder-med">
                                        <div class="reminder-med-name">${reminder.medication}</div>
                                        <div class="reminder-med-dose">${reminder.dosage}</div>
                                    </div>
                                    <i class="fas fa-bell" style="color: #f59e0b;"></i>
                                </div>
                            `;
                        });
                        document.getElementById('remindersList').innerHTML = html;
                    } else {
                        document.getElementById('remindersList').innerHTML = `
                            <p style="text-align: center; color: #64748b; padding: 20px;">
                                No medications scheduled for today
                            </p>
                        `;
                    }
                });
        }

        // Load Tips
        function loadTips() {
            const tips = [
                {
                    icon: '<i class="fas fa-clock" style="color: #ef4444;"></i>',
                    title: 'Set Reminders',
                    text: 'Take medications at the same time each day to build a routine'
                },
                {
                    icon: '<i class="fas fa-tint" style="color: #3b82f6;"></i>',
                    title: 'Stay Hydrated',
                    text: 'Always take pills with a full glass of water unless directed otherwise'
                },
                {
                    icon: '<i class="fas fa-utensils" style="color: #f59e0b;"></i>',
                    title: 'Food Interactions',
                    text: 'Some medications work better with food, others on an empty stomach'
                },
                {
                    icon: '<i class="fas fa-notes-medical" style="color: #6366f1;"></i>',
                    title: 'Track Side Effects',
                    text: 'Note any unusual symptoms and discuss with your doctor'
                }
            ];

            let html = '<div style="display: flex; flex-direction: column; gap: 12px;">';
            tips.forEach(tip => {
                html += `
                    <div style="background: #f8fafc; padding: 16px; border-radius: 12px; border-left: 4px solid #667eea;">
                        <div style="font-weight: 600; color: #0f172a; margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 20px;">${tip.icon}</span>
                            ${tip.title}
                        </div>
                        <div style="font-size: 14px; color: #64748b;">${tip.text}</div>
                    </div>
                `;
            });
            html += '</div>';
            document.getElementById('medicationTips').innerHTML = html;
        }

        // Edit Medication
        function editMedication(id) {
            // Fetch medication details and show modal
            fetch(`../api/get_medication.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const med = data.medication;
                        document.getElementById('edit_medication_id').value = med.medication_id;
                        document.getElementById('edit_medication_name').value = med.medication_name;
                        document.getElementById('edit_dosage').value = med.dosage;
                        document.getElementById('edit_frequency').value = med.frequency;
                        document.getElementById('edit_notes').value = med.notes || '';
                        document.getElementById('editModal').classList.add('active');
                    }
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // Complete Medication
        function completeMedication(id) {
            if (confirm('Mark this medication as completed?')) {
                fetch('../api/update_medication_status.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({medication_id: id, status: 'completed'})
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadMedications();
                        loadStats();
                    }
                });
            }
        }

        // Delete Medication
        function deleteMedication(id) {
            if (confirm('Are you sure you want to delete this medication?')) {
                fetch('../api/delete_medication.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({medication_id: id})
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadMedications();
                        loadStats();
                        loadReminders();
                    }
                });
            }
        }

        // Helper Functions
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        // Close modal on outside click
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
    <!-- Shared Footer -->
    <?php include('../includes/footer.php'); ?>
</body>
</html>
