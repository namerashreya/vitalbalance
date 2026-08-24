// VitalBalance Dashboard JavaScript

// Global variables for interconnected dashboard
let healthChart = null;
let currentFilter = { type: 'all', value: '' };

document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 1000,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });

    // Initialize components
    initNavbar();
    initProfileDropdown();
    initStatCounters();
    initProgressRing();
    initDiseaseCharts();
    initRecipeFilters();
    initModal();
    
    // Load dynamic wellness score
    loadWellnessScore();
    
    // Initialize health chart
    initHealthChart();
});

// Navbar scroll effect
function initNavbar() {
    const navbar = document.getElementById('navbar');
    let lastScroll = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
}

// Profile dropdown toggle
function initProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    const trigger = dropdown.querySelector('.profile-trigger');

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });
}

// Animated stat counters
function initStatCounters() {
    const statValues = document.querySelectorAll('.stat-value[data-count]');
    
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const targetCount = parseInt(element.getAttribute('data-count'));
                animateCounter(element, targetCount);
                observer.unobserve(element);
            }
        });
    }, observerOptions);

    statValues.forEach(stat => {
        observer.observe(stat);
    });
}

function animateCounter(element, target) {
    const duration = 2000; // 2 seconds
    const start = 0;
    const increment = target / (duration / 16); // 60fps
    let current = start;

    const timer = setInterval(function() {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Progress ring animation
function initProgressRing() {
    const progressCircle = document.querySelector('.progress-ring-circle');
    if (!progressCircle) return;

    const percentage = parseInt(progressCircle.getAttribute('data-percentage'));
    const circumference = 2 * Math.PI * 54; // radius = 54
    const offset = circumference - (percentage / 100) * circumference;

    // Create gradient definition
    const svg = progressCircle.closest('svg');
    if (svg) {
        const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
        gradient.setAttribute('id', 'gradient');
        gradient.setAttribute('x1', '0%');
        gradient.setAttribute('y1', '0%');
        gradient.setAttribute('x2', '100%');
        gradient.setAttribute('y2', '100%');
        
        const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
        stop1.setAttribute('offset', '0%');
        stop1.setAttribute('stop-color', '#8b5cf6');
        
        const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
        stop2.setAttribute('offset', '100%');
        stop2.setAttribute('stop-color', '#14b8a6');
        
        gradient.appendChild(stop1);
        gradient.appendChild(stop2);
        defs.appendChild(gradient);
        svg.insertBefore(defs, svg.firstChild);
    }

    // Animate progress ring
    setTimeout(() => {
        progressCircle.style.strokeDashoffset = offset;
    }, 500);
}

// Disease charts initialization
function initDiseaseCharts() {
    const chartCanvases = document.querySelectorAll('.disease-chart-canvas');
    
    chartCanvases.forEach(canvas => {
        const trend = parseFloat(canvas.getAttribute('data-trend'));
        const prevalence = parseFloat(canvas.getAttribute('data-prevalence'));
        
        // Create simple bar chart
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        // Clear canvas
        ctx.clearRect(0, 0, width, height);
        
        // Draw background bars
        ctx.fillStyle = '#e5e7eb';
        ctx.fillRect(10, height - 20, width - 20, 8);
        
        // Draw trend bar
        const barWidth = ((trend / 5) * (width - 20)); // Scale to max 5%
        ctx.fillStyle = trend > 0 ? '#ef4444' : '#10b981';
        ctx.fillRect(10, height - 20, Math.min(barWidth, width - 20), 8);
        
        // Draw prevalence indicator
        ctx.fillStyle = '#8b5cf6';
        const indicatorX = (prevalence / 20) * (width - 20); // Scale to max 20%
        ctx.beginPath();
        ctx.arc(10 + indicatorX, height - 16, 4, 0, Math.PI * 2);
        ctx.fill();
    });
}

// Recipe filters
function initRecipeFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const recipeCards = document.querySelectorAll('.recipe-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');

            // Filter recipes
            recipeCards.forEach(card => {
                const mealType = card.getAttribute('data-meal-type');
                
                if (filter === 'all' || mealType === filter) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// Modal functions
function initModal() {
    const modal = document.getElementById('addRecordModal');
    const form = document.getElementById('addRecordForm');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            saveHealthRecord();
        });
    }

    // Set default datetime to now
    const datetimeInput = form?.querySelector('input[name="recorded_at"]');
    if (datetimeInput) {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        datetimeInput.value = now.toISOString().slice(0, 16);
    }
}

function openAddRecordModal() {
    const modal = document.getElementById('addRecordModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeAddRecordModal() {
    const modal = document.getElementById('addRecordModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        
        // Reset form
        const form = document.getElementById('addRecordForm');
        if (form) {
            form.reset();
        }
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('addRecordModal');
    if (modal && e.target === modal) {
        closeAddRecordModal();
    }
});

// Save health record
function saveHealthRecord() {
    const form = document.getElementById('addRecordForm');
    const formData = new FormData(form);

    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    submitBtn.disabled = true;

    fetch('api/add_record.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Record added successfully!', 'success');
            closeAddRecordModal();
            // Update dashboard dynamically without reload
            updateDashboardAfterRecord();
        } else {
            showNotification(data.message || 'Failed to add record', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Update dashboard after adding record
function updateDashboardAfterRecord() {
    // Reload wellness score
    loadWellnessScore();
    
    // Reload records
    loadFilteredRecords();
    
    // Update stats
    updateStats();
    
    // Update chart if visible
    if (healthChart) {
        updateHealthChart();
    }
}

// Load wellness score dynamically
function loadWellnessScore() {
    fetch('api/get_wellness_score.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const scoreElement = document.getElementById('wellnessScoreValue');
                const circleElement = document.getElementById('wellnessCircle');
                const trendElement = document.getElementById('wellnessTrend');
                
                if (scoreElement) {
                    const currentScore = parseInt(scoreElement.textContent);
                    const newScore = data.wellness_score;
                    
                    // Animate score change
                    animateScoreChange(scoreElement, currentScore, newScore);
                    
                    // Update progress ring
                    if (circleElement) {
                        updateProgressRing(circleElement, newScore);
                    }
                    
                    // Update trend
                    if (trendElement) {
                        const trendText = data.trend === 'improving' ? '📈 Improving' : 
                                        data.trend === 'declining' ? '📉 Declining' : '➡️ Stable';
                        trendElement.textContent = trendText + ' - Based on your records';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error loading wellness score:', error);
        });
}

// Animate score change
function animateScoreChange(element, from, to) {
    const duration = 1000;
    const steps = 60;
    const increment = (to - from) / steps;
    let current = from;
    let step = 0;
    
    const timer = setInterval(() => {
        step++;
        current += increment;
        if (step >= steps || (increment > 0 && current >= to) || (increment < 0 && current <= to)) {
            element.textContent = to + '%';
            clearInterval(timer);
        } else {
            element.textContent = Math.round(current) + '%';
        }
    }, duration / steps);
}

// Update progress ring
function updateProgressRing(circle, percentage) {
    const circumference = 2 * Math.PI * 54;
    const offset = circumference - (percentage / 100) * circumference;
    circle.style.strokeDashoffset = offset;
    circle.setAttribute('data-percentage', percentage);
}

// Filter dashboard by type and value
function filterDashboard(filterType, filterValue) {
    currentFilter = { type: filterType, value: filterValue };
    
    // Update active stat card
    document.querySelectorAll('.stat-card').forEach(card => {
        card.classList.remove('active-filter');
        if (card.getAttribute('data-filter') === filterType && 
            card.getAttribute('data-filter-value') === filterValue) {
            card.classList.add('active-filter');
        }
    });
    
    // Load filtered data
    loadFilteredRecords();
    loadFilteredRecipes();
    
    // Scroll to relevant section
    if (filterType === 'record_type') {
        document.getElementById('healthRecordsSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else if (filterType === 'disease') {
        document.getElementById('recipes-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Filter by disease
function filterByDisease(diseaseId, diseaseName) {
    currentFilter = { type: 'disease', value: diseaseId };
    
    showNotification(`Filtering by ${diseaseName}`, 'info');
    
    // Update record filter
    document.getElementById('recordFilter').value = 'all';
    
    // Load related data
    loadFilteredRecords();
    loadFilteredRecipes();
    
    // Scroll to recipes section
    document.getElementById('recipes-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Load filtered records
function loadFilteredRecords() {
    const filterValue = currentFilter.type === 'record_type' ? currentFilter.value : 
                       document.getElementById('recordFilter')?.value || 'all';
    
    fetch(`api/get_dashboard_data.php?filter_type=record_type&filter_value=${filterValue}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateRecordsDisplay(data.records);
            }
        })
        .catch(error => {
            console.error('Error loading records:', error);
        });
}

// Update records display
function updateRecordsDisplay(records) {
    const container = document.getElementById('recordsContainer');
    if (!container) return;
    
    const timeline = container.querySelector('.records-timeline');
    if (!timeline) return;
    
    // Clear existing records (except template)
    const existingRecords = timeline.querySelectorAll('.record-item');
    existingRecords.forEach(item => {
        if (item.getAttribute('data-record-type') && 
            currentFilter.type === 'record_type' && 
            currentFilter.value !== 'all' &&
            item.getAttribute('data-record-type') !== currentFilter.value) {
            item.style.display = 'none';
        } else {
            item.style.display = '';
        }
    });
    
    // If no records match, show message
    const visibleRecords = Array.from(timeline.querySelectorAll('.record-item'))
        .filter(item => item.style.display !== 'none');
    
    if (visibleRecords.length === 0 && currentFilter.value !== 'all') {
        const emptyMsg = document.createElement('div');
        emptyMsg.className = 'empty-state';
        emptyMsg.innerHTML = `
            <div class="empty-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3>No records found</h3>
            <p>No records match the current filter</p>
        `;
        timeline.appendChild(emptyMsg);
    }
}

// Filter records by type
function filterRecords(recordType) {
    currentFilter = { type: 'record_type', value: recordType };
    loadFilteredRecords();
    
    // Update stat card
    document.querySelectorAll('.stat-card').forEach(card => {
        card.classList.remove('active-filter');
    });
}

// Load filtered recipes
function loadFilteredRecipes() {
    const filterValue = currentFilter.type === 'disease' ? currentFilter.value : '';
    
    fetch(`api/get_dashboard_data.php?filter_type=disease&filter_value=${filterValue}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recipes are already loaded, just highlight if filtered
                if (currentFilter.type === 'disease') {
                    showNotification('Showing recipes related to selected condition', 'info');
                }
            }
        })
        .catch(error => {
            console.error('Error loading recipes:', error);
        });
}

// Initialize health chart
function initHealthChart() {
    const canvas = document.getElementById('healthTrendsChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    healthChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
    
    updateHealthChart();
}

// Update health chart
function updateHealthChart() {
    const recordType = document.getElementById('chartRecordType')?.value || 'all';
    
    fetch(`api/get_health_chart_data.php?record_type=${recordType}&days=30`)
        .then(response => response.json())
        .then(data => {
            if (data.success && healthChart) {
                healthChart.data.labels = data.labels;
                healthChart.data.datasets = data.datasets;
                healthChart.update();
            }
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
        });
}

// Show health chart
function showHealthChart() {
    const chartSection = document.getElementById('healthChartSection');
    if (chartSection) {
        chartSection.style.display = 'block';
        chartSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        updateHealthChart();
    }
}

// Update stats
function updateStats() {
    fetch('api/get_dashboard_data.php?filter_type=all')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.stats) {
                // Update stat values
                const diseaseCount = document.querySelector('.stat-card[data-filter="disease"] .stat-value[data-count]');
                const recordsCount = document.querySelector('.stat-card[data-filter="record_type"] .stat-value[data-count]');
                
                if (diseaseCount && data.stats.disease_count !== undefined) {
                    animateCounter(diseaseCount, data.stats.disease_count);
                    diseaseCount.setAttribute('data-count', data.stats.disease_count);
                }
                
                if (recordsCount && data.stats.records_count !== undefined) {
                    animateCounter(recordsCount, data.stats.records_count);
                    recordsCount.setAttribute('data-count', data.stats.records_count);
                }
            }
        })
        .catch(error => {
            console.error('Error updating stats:', error);
        });
}

// ============================================
// ADD DISEASE FUNCTIONALITY
// ============================================

let allDiseases = [];
let selectedDisease = null;
let searchTimeout = null;

// Open Add Disease Modal
function openAddDiseaseModal() {
    const modal = document.getElementById('addDiseaseModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        loadDiseases();
        
        // Set default date to today
        const dateInput = document.getElementById('diagnosedDate');
        if (dateInput) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }
    }
}

// Close Add Disease Modal
function closeAddDiseaseModal() {
    const modal = document.getElementById('addDiseaseModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        
        // Reset form
        const form = document.getElementById('addDiseaseForm');
        if (form) {
            form.reset();
        }
        
        // Reset selection
        selectedDisease = null;
        document.getElementById('selectedDiseaseId').value = '';
        document.getElementById('selectedDiseaseInfo').style.display = 'none';
        document.querySelectorAll('.disease-option-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Clear search
        const searchInput = document.getElementById('diseaseSearch');
        if (searchInput) {
            searchInput.value = '';
        }
        const searchResults = document.getElementById('diseaseSearchResults');
        if (searchResults) {
            searchResults.classList.remove('show');
            searchResults.innerHTML = '';
        }
    }
}

// Load all diseases
function loadDiseases() {
    const diseaseList = document.getElementById('diseaseList');
    if (!diseaseList) return;
    
    diseaseList.innerHTML = '<div class="loading-state"><i class="fas fa-spinner fa-spin"></i> Loading diseases...</div>';
    
    fetch('api/get_diseases.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.diseases) {
                allDiseases = data.diseases;
                displayDiseases(data.diseases);
            } else {
                diseaseList.innerHTML = '<div class="empty-state">No diseases available</div>';
            }
        })
        .catch(error => {
            console.error('Error loading diseases:', error);
            diseaseList.innerHTML = '<div class="empty-state">Error loading diseases. Please try again.</div>';
        });
}

// Display diseases in grid
function displayDiseases(diseases) {
    const diseaseList = document.getElementById('diseaseList');
    if (!diseaseList) return;
    
    if (diseases.length === 0) {
        diseaseList.innerHTML = '<div class="empty-state">No diseases found</div>';
        return;
    }
    
    diseaseList.innerHTML = diseases.map(disease => {
        const hasDisease = disease.user_has ? 'disabled' : '';
        const icon = disease.icon || '🩺';
        const color = disease.color_code || '#8b5cf6';
        
        return `
            <div class="disease-option-card ${hasDisease}" 
                 data-disease-id="${disease.disease_id}"
                 onclick="${hasDisease ? '' : `selectDisease(${disease.disease_id})`}"
                 style="${hasDisease ? 'cursor: not-allowed;' : ''}">
                <div class="disease-option-check">
                    <i class="fas fa-check"></i>
                </div>
                <div class="disease-option-icon" style="color: ${color};">
                    ${icon}
                </div>
                <div class="disease-option-name">${escapeHtml(disease.disease_name)}</div>
                ${disease.category ? `<div class="disease-option-category">${escapeHtml(disease.category)}</div>` : ''}
                ${hasDisease ? '<div style="font-size: 0.7rem; color: #ef4444; margin-top: 0.25rem;">Already Added</div>' : ''}
            </div>
        `;
    }).join('');
}

// Search diseases
function searchDiseases(query) {
    const searchResults = document.getElementById('diseaseSearchResults');
    if (!searchResults) return;
    
    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    if (!query || query.length < 2) {
        searchResults.classList.remove('show');
        searchResults.innerHTML = '';
        return;
    }
    
    // Debounce search
    searchTimeout = setTimeout(() => {
        fetch(`api/get_diseases.php?search=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.diseases) {
                    displaySearchResults(data.diseases);
                } else {
                    searchResults.innerHTML = '<div class="disease-search-item">No results found</div>';
                    searchResults.classList.add('show');
                }
            })
            .catch(error => {
                console.error('Error searching diseases:', error);
            });
    }, 300);
}

// Display search results
function displaySearchResults(diseases) {
    const searchResults = document.getElementById('diseaseSearchResults');
    if (!searchResults) return;
    
    if (diseases.length === 0) {
        searchResults.innerHTML = '<div class="disease-search-item">No results found</div>';
        searchResults.classList.add('show');
        return;
    }
    
    searchResults.innerHTML = diseases
        .filter(d => !d.user_has) // Only show diseases user doesn't have
        .map(disease => {
            const icon = disease.icon || '🩺';
            const color = disease.color_code || '#8b5cf6';
            return `
                <div class="disease-search-item" onclick="selectDiseaseFromSearch(${disease.disease_id})">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.5rem;">${icon}</span>
                        <div>
                            <div style="font-weight: 600; color: #1f2937;">${escapeHtml(disease.disease_name)}</div>
                            ${disease.category ? `<div style="font-size: 0.75rem; color: #6b7280;">${escapeHtml(disease.category)}</div>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    
    searchResults.classList.add('show');
}

// Select disease from search
function selectDiseaseFromSearch(diseaseId) {
    selectDisease(diseaseId);
    document.getElementById('diseaseSearch').value = '';
    document.getElementById('diseaseSearchResults').classList.remove('show');
}

// Select disease
function selectDisease(diseaseId) {
    const disease = allDiseases.find(d => d.disease_id == diseaseId);
    if (!disease || disease.user_has) return;
    
    selectedDisease = disease;
    
    // Update hidden input
    document.getElementById('selectedDiseaseId').value = diseaseId;
    
    // Update selected state in grid
    document.querySelectorAll('.disease-option-card').forEach(card => {
        card.classList.remove('selected');
        if (card.getAttribute('data-disease-id') == diseaseId) {
            card.classList.add('selected');
        }
    });
    
    // Show selected disease info
    const infoDiv = document.getElementById('selectedDiseaseInfo');
    const iconDiv = document.getElementById('selectedDiseaseIcon');
    const nameDiv = document.getElementById('selectedDiseaseName');
    const descDiv = document.getElementById('selectedDiseaseDesc');
    
    if (infoDiv && iconDiv && nameDiv && descDiv) {
        iconDiv.textContent = disease.icon || '🩺';
        iconDiv.style.background = `${disease.color_code || '#8b5cf6'}20`;
        iconDiv.style.color = disease.color_code || '#8b5cf6';
        nameDiv.textContent = disease.disease_name;
        descDiv.textContent = disease.description || 'No description available';
        infoDiv.style.display = 'block';
        
        // Scroll to selected info
        infoDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const addDiseaseForm = document.getElementById('addDiseaseForm');
    if (addDiseaseForm) {
        addDiseaseForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveDisease();
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('addDiseaseModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeAddDiseaseModal();
            }
        });
    }
    
    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        const searchInput = document.getElementById('diseaseSearch');
        const searchResults = document.getElementById('diseaseSearchResults');
        if (searchInput && searchResults && !searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('show');
        }
    });
});

// Save disease
function saveDisease() {
    const form = document.getElementById('addDiseaseForm');
    const formData = new FormData(form);
    
    // Validate
    if (!formData.get('disease_id')) {
        showNotification('Please select a disease', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    submitBtn.disabled = true;
    
    fetch('api/add_disease.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Disease added successfully!', 'success');
            closeAddDiseaseModal();
            
            // Update dashboard
            updateDashboardAfterDisease();
        } else {
            showNotification(data.message || 'Failed to add disease', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Update dashboard after adding disease
function updateDashboardAfterDisease() {
    // Reload the page to show new disease
    // In a real app, you'd update via AJAX
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Delete user disease
function deleteUserDisease(userDiseaseId) {
    if (!confirm('Are you sure you want to remove this disease from your list?')) {
        return;
    }
    
    fetch('api/delete_user_disease.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ user_disease_id: userDiseaseId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Disease removed successfully!', 'success');
            // Remove from DOM
            const card = document.querySelector(`.my-disease-card[data-user-disease-id="${userDiseaseId}"]`);
            if (card) {
                card.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => {
                    card.remove();
                    // Update stats
                    updateStats();
                    // Check if no diseases left
                    const container = document.getElementById('myDiseasesContainer');
                    if (container && container.querySelectorAll('.my-disease-card').length === 0) {
                        location.reload();
                    }
                }, 300);
            } else {
                // Fallback: reload page
                setTimeout(() => {
                    updateDashboardAfterDisease();
                }, 500);
            }
        } else {
            showNotification(data.message || 'Failed to remove disease', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

// Edit user disease
function editUserDisease(userDiseaseId) {
    // TODO: Implement edit functionality
    showNotification('Edit functionality coming soon!', 'info');
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Edit record
function editRecord(recordId) {
    // TODO: Implement edit functionality
    showNotification('Edit functionality coming soon!', 'info');
}

// Delete record
function deleteRecord(recordId) {
    if (!confirm('Are you sure you want to delete this record?')) {
        return;
    }

    fetch('api/delete_record.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ record_id: recordId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Record deleted successfully!', 'success');
            // Update dashboard dynamically
            updateDashboardAfterRecord();
            
            // Remove the record from DOM
            const recordElement = document.querySelector(`.record-item[data-record-id="${recordId}"]`);
            if (recordElement) {
                recordElement.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => {
                    recordElement.remove();
                }, 300);
            }
        } else {
            showNotification(data.message || 'Failed to delete record', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

// Toggle favorite recipe
function toggleFavorite(recipeId) {
    fetch('api/toggle_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ recipe_id: recipeId })
    })
    .then(response => response.json())
    .then(data => {
        const btn = event.target.closest('.btn-favorite');
        const icon = btn.querySelector('i');
        
        if (data.is_favorite) {
            icon.classList.remove('far');
            icon.classList.add('fas');
            showNotification('Recipe added to favorites!', 'success');
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
            showNotification('Recipe removed from favorites', 'info');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

// View recipe
function viewRecipe(recipeId) {
    window.location.href = `recipe_details.php?id=${recipeId}`;
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function getNotificationIcon(type) {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    return icons[type] || icons.info;
}

// Add notification styles
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    .notification {
        position: fixed;
        top: 90px;
        right: 2rem;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        z-index: 3000;
        opacity: 0;
        transform: translateX(400px);
        transition: all 0.3s ease;
        max-width: 400px;
    }

    .notification.show {
        opacity: 1;
        transform: translateX(0);
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .notification-success {
        border-left: 4px solid #10b981;
    }

    .notification-success i {
        color: #10b981;
    }

    .notification-error {
        border-left: 4px solid #ef4444;
    }

    .notification-error i {
        color: #ef4444;
    }

    .notification-info {
        border-left: 4px solid #3b82f6;
    }

    .notification-info i {
        color: #3b82f6;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(notificationStyles);

