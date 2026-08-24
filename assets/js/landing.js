// VitalBalance Landing Page JavaScript

// Initialize AOS (Animate On Scroll)
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 1000,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });

    // Create animated particles
    createParticles();
    
    // Form tab switching
    initFormTabs();
    
    // Form validation
    initFormValidation();
    
    // Form submission handlers
    initFormSubmission();
});

// Create floating particles background
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    if (!particlesContainer) return;

    const particleCount = 50;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Random size between 2px and 6px
        const size = Math.random() * 4 + 2;
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        
        // Random position
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        
        // Random animation delay and duration
        particle.style.animationDelay = Math.random() * 15 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
        
        // Random opacity
        particle.style.opacity = Math.random() * 0.5 + 0.2;
        
        particlesContainer.appendChild(particle);
    }
}

// Form tab switching
function initFormTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const forms = document.querySelectorAll('.auth-form');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetForm = this.getAttribute('data-form');
            
            // Remove active class from all tabs and forms
            tabButtons.forEach(btn => btn.classList.remove('active'));
            forms.forEach(form => form.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding form
            this.classList.add('active');
            document.getElementById(targetForm + 'Form').classList.add('active');
            
            // Clear any error messages
            const alertMessage = document.getElementById('alertMessage');
            if (alertMessage) {
                alertMessage.style.display = 'none';
            }
        });
    });
}

// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const toggle = input.nextElementSibling;
    const icon = toggle.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Form validation
function initFormValidation() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    // Login form validation
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            if (!email || !password) {
                e.preventDefault();
                showAlert('Please fill in all fields', 'error');
                return false;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                showAlert('Please enter a valid email address', 'error');
                return false;
            }
            
            // Show loading state
            showLoadingState(loginForm);
        });
    }
    
    // Register form validation
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const name = document.getElementById('registerName').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.querySelector('input[name="terms"]').checked;
            
            // Clear previous errors
            clearErrors();
            
            let hasError = false;
            
            if (!name || name.trim().length < 2) {
                showFieldError('registerName', 'Name must be at least 2 characters');
                hasError = true;
            }
            
            if (!email || !isValidEmail(email)) {
                showFieldError('registerEmail', 'Please enter a valid email address');
                hasError = true;
            }
            
            if (!password || password.length < 6) {
                showFieldError('registerPassword', 'Password must be at least 6 characters');
                hasError = true;
            }
            
            if (password !== confirmPassword) {
                showFieldError('confirmPassword', 'Passwords do not match');
                hasError = true;
            }
            
            if (!terms) {
                showAlert('Please agree to the Terms & Conditions', 'error');
                hasError = true;
            }
            
            if (hasError) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            showLoadingState(registerForm);
        });
        
        // Real-time password match validation
        const confirmPasswordInput = document.getElementById('confirmPassword');
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('blur', function() {
                const password = document.getElementById('registerPassword').value;
                if (this.value && password !== this.value) {
                    showFieldError('confirmPassword', 'Passwords do not match');
                } else {
                    clearFieldError('confirmPassword');
                }
            });
        }
    }
}

// Email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Show field error
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    // Remove existing error
    clearFieldError(fieldId);
    
    // Add error class
    field.classList.add('error-field');
    field.style.borderColor = '#ef4444';
    
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = '#ef4444';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    
    field.parentElement.appendChild(errorDiv);
}

// Clear field error
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    field.classList.remove('error-field');
    field.style.borderColor = '';
    
    const errorDiv = field.parentElement.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Clear all errors
function clearErrors() {
    document.querySelectorAll('.field-error').forEach(el => el.remove());
    document.querySelectorAll('.error-field').forEach(el => {
        el.classList.remove('error-field');
        el.style.borderColor = '';
    });
}

// Show alert message
function showAlert(message, type = 'error') {
    // Remove existing alerts
    const existingAlert = document.getElementById('alertMessage');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.id = 'alertMessage';
    alertDiv.className = `alert alert-${type}`;
    
    const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
    alertDiv.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
    `;
    
    // Insert after form tabs
    const formTabs = document.querySelector('.form-tabs');
    if (formTabs) {
        formTabs.parentElement.insertBefore(alertDiv, formTabs.nextSibling);
    }
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => alertDiv.remove(), 300);
        }
    }, 5000);
}

// Show loading state on form submission
function showLoadingState(form) {
    const submitButton = form.querySelector('.btn-submit');
    const btnText = submitButton.querySelector('.btn-text');
    const btnLoader = submitButton.querySelector('.btn-loader');
    
    if (btnText && btnLoader) {
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline-block';
        submitButton.disabled = true;
        submitButton.style.opacity = '0.7';
        submitButton.style.cursor = 'not-allowed';
    }
}

// Form submission handlers
function initFormSubmission() {
    // Handle successful registration - switch to login tab
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        // Check if there's a success message (from PHP)
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            // Switch to login tab after 2 seconds
            setTimeout(() => {
                const loginTab = document.querySelector('.tab-btn[data-form="login"]');
                if (loginTab) {
                    loginTab.click();
                }
            }, 2000);
        }
    }
}

// Add fadeOut animation for alerts
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }
`;
document.head.appendChild(style);

