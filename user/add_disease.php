<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

// Get current user
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Add Disease</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .form-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(148, 163, 184, 0.25);
            max-width: 800px;
            margin: 24px auto;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px 24px;
            margin-top: 16px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }
        select, input[type="date"], textarea {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        select:focus, input[type="date"]:focus, textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.1);
        }
        textarea {
            min-height: 90px;
            resize: vertical;
        }
        .helper-text {
            font-size: 12px;
            color: #64748b;
        }
        .status-pill-group {
            display: flex;
            gap: 8px;
        }
        .status-pill {
            flex: 1;
            text-align: center;
            padding: 8px 10px;
            border-radius: 999px;
            border: 1px solid #cbd5e1;
            font-size: 13px;
            cursor: pointer;
            user-select: none;
            transition: all 0.15s ease;
        }
        .status-pill.active {
            background: #f97316;
            border-color: #ea580c;
            color: #fff;
        }
        .status-pill.managed {
            background: #22c55e;
            border-color: #16a34a;
            color: #fff;
        }
        .status-pill.recovered {
            background: #10b981;
            border-color: #059669;
            color: #fff;
        }
        .status-pill.inactive {
            background: #f8fafc;
            color: #0f172a;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
        }
        .btn-outline {
            background: #f8fafc;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-primary {
            border-radius: 999px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
        }
        .page-header-inline {
            max-width: 800px;
            margin: 24px auto 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .page-header-inline h1 {
            font-size: 22px;
            margin: 0;
        }
        .alert {
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 12px;
        }
        .alert-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .alert-success {
            background: #ecfdf3;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
    </style>
</head>
<body>
    <!-- Header (reuse dashboard style) -->
    <header class="header">
        <div class="header-content">
            <div>
                <div class="logo">VitalBalance</div>
                <div class="logo-subtitle">Personalized Wellness & Chronic Care</div>
            </div>
            <div class="header-right">
                <a href="dashboard.php" class="logout-btn" style="background:#e5e7eb;color:#111827;">Back to Dashboard</a>
                <a href="../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <main class="main-container">
        <div class="page-header-inline">
            <div>
                <h1>Add a Chronic Disease</h1>
                <p class="page-subtitle">Select a condition and personalize it with your diagnosis details.</p>
            </div>
            <a href="my_diseases.php" class="btn btn-secondary">My Chronic Diseases</a>
        </div>

        <div class="form-card">
            <div id="form-alert" style="display:none;"></div>

            <form id="add-disease-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="disease_id">Disease</label>
                        <select id="disease_id" name="disease_id" required>
                            <option value="">Loading diseases...</option>
                        </select>
                        <p class="helper-text">Start typing to quickly find Diabetes, Hypertension, Asthma, and more.</p>
                    </div>

                    <div class="form-group">
                        <label for="diagnosed_date">Diagnosed Date</label>
                        <input type="date" id="diagnosed_date" name="diagnosed_date">
                        <p class="helper-text">Optional, but helps track how long you’ve been managing this condition.</p>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <input type="hidden" id="status" name="status" value="active">
                        <div class="status-pill-group">
                            <div class="status-pill active" data-status="active">Active</div>
                            <div class="status-pill inactive" data-status="managed">Managed</div>
                            <div class="status-pill inactive" data-status="recovered">Recovered</div>
                        </div>
                        <p class="helper-text">How is this condition currently for you?</p>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" placeholder="Doctor, medications, triggers, or anything else you want to remember..."></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="dashboard.php" class="btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Disease</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Status pill interaction
        document.querySelectorAll('.status-pill').forEach(function(pill) {
            pill.addEventListener('click', function() {
                var status = this.getAttribute('data-status');
                document.getElementById('status').value = status;

                document.querySelectorAll('.status-pill').forEach(function(p) {
                    p.classList.add('inactive');
                    p.classList.remove('active', 'managed', 'recovered');
                });

                this.classList.remove('inactive');
                this.classList.add(status);
            });
        });

        function showAlert(type, message) {
            var el = document.getElementById('form-alert');
            el.className = 'alert ' + (type === 'error' ? 'alert-error' : 'alert-success');
            el.textContent = message;
            el.style.display = 'block';
        }

        function clearAlert() {
            var el = document.getElementById('form-alert');
            el.style.display = 'none';
            el.textContent = '';
        }

        // Load diseases from API
        function loadDiseases() {
            var select = document.getElementById('disease_id');
            fetch('../api/get_diseases.php')
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    select.innerHTML = '';
                    if (!data.success || !data.diseases || data.diseases.length === 0) {
                        var opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = 'No diseases configured';
                        select.appendChild(opt);
                        select.disabled = true;
                        return;
                    }

                    var placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = 'Select a disease';
                    select.appendChild(placeholder);

                    // Group diseases by category
                    var categories = {};
                    data.diseases.forEach(function(disease) {
                        var category = disease.category || 'Other';
                        if (!categories[category]) {
                            categories[category] = [];
                        }
                        categories[category].push(disease);
                    });

                    // Sort categories alphabetically
                    var sortedCategories = Object.keys(categories).sort();

                    // Create optgroups for each category
                    sortedCategories.forEach(function(category) {
                        var optgroup = document.createElement('optgroup');
                        optgroup.label = category;
                        
                        // Sort diseases within category
                        categories[category].sort(function(a, b) {
                            return a.disease_name.localeCompare(b.disease_name);
                        });

                        categories[category].forEach(function(disease) {
                            var opt = document.createElement('option');
                            opt.value = disease.disease_id;
                            opt.textContent = disease.disease_name;
                            if (disease.user_has) {
                                opt.textContent += ' (already added)';
                                opt.disabled = true;
                            }
                            optgroup.appendChild(opt);
                        });
                        
                        select.appendChild(optgroup);
                    });
                })
                .catch(function() {
                    select.innerHTML = '<option value=\"\">Failed to load diseases</option>';
                });
        }

        document.getElementById('add-disease-form').addEventListener('submit', function(e) {
            e.preventDefault();
            clearAlert();

            var formData = new FormData(this);

            fetch('../api/add_disease.php', {
                method: 'POST',
                body: formData
            })
            .then(function(res) { 
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json(); 
            })
            .then(function(data) {
                if (!data.success) {
                    showAlert('error', data.message || 'Failed to add disease.');
                    console.error('API Error:', data);
                    return;
                }
                showAlert('success', 'Disease added successfully. Redirecting to My Diseases...');
                setTimeout(function() {
                    window.location.href = 'my_diseases.php';
                }, 1200);
            })
            .catch(function(error) {
                console.error('Error:', error);
                showAlert('error', 'Something went wrong. Please check the console for details or try again.');
            });
        });

        loadDiseases();
    </script>
</body>
</html>


