/**
 * ========================================
 * VITALBALANCE MODERN UX UTILITIES
 * Toast Notifications | Form Validation | Micro-interactions
 * ========================================
 */

// ============ TOAST NOTIFICATION SYSTEM ============
class VBToast {
    constructor() {
        this.container = this.createContainer();
    }

    createContainer() {
        let container = document.getElementById('vb-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'vb-toast-container';
            container.style.cssText = `
                position: fixed;
                top: 90px;
                right: 20px;
                z-index: 1060;
                display: flex;
                flex-direction: column;
                gap: 12px;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }
        return container;
    }

    show(message, type = 'success', duration = 4000) {
        const toast = document.createElement('div');
        toast.className = `vb-toast vb-toast-${type}`;

        const icons = {
            success: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            error: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            warning: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
            info: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };

        toast.innerHTML = `
            <div class="vb-toast-icon">${icons[type]}</div>
            <div class="vb-toast-message">${message}</div>
            <button class="vb-toast-close" onclick="this.parentElement.remove()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;

        this.container.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('vb-toast-show'), 10);

        // Auto remove
        if (duration > 0) {
            setTimeout(() => {
                toast.classList.remove('vb-toast-show');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        return toast;
    }

    success(message, duration) {
        return this.show(message, 'success', duration);
    }

    error(message, duration) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration) {
        return this.show(message, 'info', duration);
    }
}

// Global toast instance
window.vbToast = new VBToast();

// ============ FORM VALIDATION SYSTEM ============
class VBFormValidator {
    constructor(formElement) {
        this.form = formElement;
        this.fields = {};
        this.init();
    }

    init() {
        // Add real-time validation to all inputs
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            // Validation on blur
            input.addEventListener('blur', () => this.validateField(input));

            // Real-time validation on input (with debounce)
            let timeout;
            input.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => this.validateField(input), 500);
            });
        });

        // Prevent default form submission
        this.form.addEventListener('submit', (e) => {
            if (!this.validateAll()) {
                e.preventDefault();
                vbToast.error('Please fix the errors in the form');
            }
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        const required = field.hasAttribute('required');
        const minLength = field.getAttribute('minlength');
        const maxLength = field.getAttribute('maxlength');
        const pattern = field.getAttribute('pattern');

        let error = '';

        // Required validation
        if (required && !value) {
            error = 'This field is required';
        }
        // Email validation
        else if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                error = 'Please enter a valid email address';
            }
        }
        // Min length validation
        else if (minLength && value.length < minLength) {
            error = `Minimum ${minLength} characters required`;
        }
        // Max length validation
        else if (maxLength && value.length > maxLength) {
            error = `Maximum ${maxLength} characters allowed`;
        }
        // Pattern validation
        else if (pattern && value) {
            const regex = new RegExp(pattern);
            if (!regex.test(value)) {
                error = field.getAttribute('data-error-message') || 'Invalid format';
            }
        }
        // Number validation
        else if (type === 'number' && value) {
            const min = field.getAttribute('min');
            const max = field.getAttribute('max');
            const numValue = parseFloat(value);

            if (min && numValue < parseFloat(min)) {
                error = `Minimum value is ${min}`;
            } else if (max && numValue > parseFloat(max)) {
                error = `Maximum value is ${max}`;
            }
        }

        this.showFieldError(field, error);
        return !error;
    }

    showFieldError(field, error) {
        // Remove existing error
        const existingError = field.parentElement.querySelector('.vb-field-error');
        if (existingError) existingError.remove();

        // Remove success state
        field.classList.remove('vb-field-success');
        field.classList.remove('vb-field-error');

        if (error) {
            // Add error state
            field.classList.add('vb-field-error');

            // Create error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'vb-field-error-message';
            errorDiv.innerHTML = `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                ${error}
            `;
            field.parentElement.appendChild(errorDiv);
        } else if (field.value.trim()) {
            // Add success state
            field.classList.add('vb-field-success');
        }
    }

    validateAll() {
        const inputs = this.form.querySelectorAll('input, textarea, select');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }
}

// Auto-initialize forms with validation
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-vb-validate]').forEach(form => {
        new VBFormValidator(form);
    });
});

// ============ LOADING STATES ============
function showLoading(button) {
    button.disabled = true;
    button.dataset.originalText = button.innerHTML;
    button.innerHTML = `
        <svg class="vb-spinner" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"/>
            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" opacity="0.75"/>
        </svg>
        Loading...
    `;
}

function hideLoading(button) {
    button.disabled = false;
    if (button.dataset.originalText) {
        button.innerHTML = button.dataset.originalText;
    }
}

// ============ SKELETON LOADER ============
function showSkeleton(container) {
    container.innerHTML = `
        <div class="vb-skeleton">
            <div class="vb-skeleton-line"></div>
            <div class="vb-skeleton-line"></div>
            <div class="vb-skeleton-line" style="width: 60%;"></div>
        </div>
    `;
}

// ============ MICRO-INTERACTIONS ============
// Add ripple effect to buttons
document.addEventListener('click', function (e) {
    const button = e.target.closest('.btn, .vb-btn');
    if (!button) return;

    const ripple = document.createElement('span');
    ripple.className = 'vb-ripple';

    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;

    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';

    button.appendChild(ripple);

    setTimeout(() => ripple.remove(), 600);
});

// ============ UTILITY FUNCTIONS ============
// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Format date nicely
function formatDate(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString('en-US', options);
}

// Smooth scroll to element
function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Copy to clipboard with feedback
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        vbToast.success('Copied to clipboard!');
    }).catch(() => {
        vbToast.error('Failed to copy');
    });
}

console.log('✅ VitalBalance UX Utilities Loaded');
