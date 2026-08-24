/**
 * VitalBalance Toast Notification System
 * Calm, smooth notifications instead of alerts
 */

class ToastManager {
    constructor() {
        this.container = null;
        this.toasts = [];
        this.init();
    }

    init() {
        // Create toast container if it doesn't exist
        if (!document.querySelector('.toast-container')) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        } else {
            this.container = document.querySelector('.toast-container');
        }
    }

    /**
     * Show a toast notification
     * @param {string} message - The message to display
     * @param {string} type - Type: 'success', 'error', 'warning', 'info'
     * @param {number} duration - Duration in ms (default: 4000)
     * @param {string} title - Optional title
     */
    show(message, type = 'info', duration = 4000, title = '') {
        const toast = this.createToast(message, type, title);
        this.container.appendChild(toast);
        this.toasts.push(toast);

        // Trigger animation
        setTimeout(() => toast.classList.add('active'), 10);

        // Auto remove
        if (duration > 0) {
            setTimeout(() => this.remove(toast), duration);
        }

        return toast;
    }

    createToast(message, type, title) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;

        // Get icon based on type
        const iconMap = {
            success: Icons.checkCircle,
            error: Icons.alertCircle,
            warning: Icons.alertTriangle,
            info: Icons.info
        };

        const icon = iconMap[type] || iconMap.info;

        // Default titles
        const defaultTitles = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Information'
        };

        const toastTitle = title || defaultTitles[type];

        toast.innerHTML = `
            <div class="toast-icon">
                ${icon}
            </div>
            <div class="toast-content">
                <div class="toast-title">${toastTitle}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" aria-label="Close notification">
                ${Icons.x}
            </button>
        `;

        // Close button functionality
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => this.remove(toast));

        return toast;
    }

    remove(toast) {
        toast.style.animation = 'slideOutRight 0.3s ease forwards';

        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
            this.toasts = this.toasts.filter(t => t !== toast);
        }, 300);
    }

    // Convenience methods
    success(message, title = '') {
        return this.show(message, 'success', 4000, title);
    }

    error(message, title = '') {
        return this.show(message, 'error', 5000, title);
    }

    warning(message, title = '') {
        return this.show(message, 'warning', 4500, title);
    }

    info(message, title = '') {
        return this.show(message, 'info', 4000, title);
    }

    // Clear all toasts
    clearAll() {
        this.toasts.forEach(toast => this.remove(toast));
    }
}

// Add slideOutRight animation to CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Create global instance
const toast = new ToastManager();

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ToastManager, toast };
}
