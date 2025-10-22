/**
 * Modern TaxMindr JavaScript
 * Bootstrap 5 + Custom Interactions
 */

// Apply sidebar state IMMEDIATELY before DOM loads to prevent flash
(function() {
    if (window.innerWidth >= 992) {
        const sidebarState = localStorage.getItem('sidebarState');
        if (sidebarState === 'closed') {
            // Add inline styles to prevent any layout shift
            document.documentElement.style.setProperty('--sidebar-initial-state', 'closed');
        }
    }
})();

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    initTooltips();
    
    // Sidebar toggle for mobile
    initSidebar();
    
    // Form validation
    initFormValidation();
    
    // Auto-format inputs
    initAutoFormat();
    
    // Deadline countdown
    initCountdowns();
    
    // Flash messages auto-dismiss
    initFlashMessages();
});

/**
 * Initialize Bootstrap Tooltips
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Sidebar Toggle for Mobile
 */
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const toggleButtons = document.querySelectorAll('[data-sidebar-toggle]');
    const mainWrapper = document.querySelector('.main-wrapper');
    const pageHeader = document.querySelector('.page-header');
    
    if (!sidebar) return;
    
    // Load sidebar state from localStorage (desktop only)
    if (window.innerWidth >= 992) {
        const sidebarState = localStorage.getItem('sidebarState');
        if (sidebarState === 'closed') {
            // Apply state immediately WITHOUT transitions
            sidebar.classList.add('closed');
            if (mainWrapper) mainWrapper.classList.add('sidebar-closed');
            if (pageHeader) pageHeader.classList.add('sidebar-closed');
        }
        
        // Add transitions AFTER state is restored (next frame)
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                if (mainWrapper) mainWrapper.style.transition = 'margin-left 0.3s ease';
                if (pageHeader) pageHeader.style.transition = 'left 0.3s ease';
            });
        });
    }
    
    // Toggle sidebar via burger menu
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            // Desktop: toggle closed class
            if (window.innerWidth >= 992) {
                sidebar.classList.toggle('closed');
                if (mainWrapper) mainWrapper.classList.toggle('sidebar-closed');
                if (pageHeader) pageHeader.classList.toggle('sidebar-closed');
                
                // Save state to localStorage
                const isClosed = sidebar.classList.contains('closed');
                localStorage.setItem('sidebarState', isClosed ? 'closed' : 'open');
            } 
            // Mobile: toggle show class (slide in/out)
            else {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
                document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
            }
        });
    }
    
    // Toggle sidebar via data attribute buttons
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        });
    });
    
    // Close sidebar when clicking overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        });
    }
    
    // Close sidebar when clicking a link (mobile only)
    if (window.innerWidth < 992) {
        const sidebarLinks = sidebar.querySelectorAll('.nav-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            });
        });
    }
}

/**
 * Form Validation with Bootstrap
 */
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            // Custom validations
            const tinInput = form.querySelector('input[name="tin"]');
            if (tinInput && tinInput.value) {
                if (!validateTIN(tinInput.value)) {
                    event.preventDefault();
                    showFieldError(tinInput, 'Invalid TIN format (XXX-XXX-XXX-XXX)');
                }
            }
            
            const mobileInput = form.querySelector('input[name="mobile_number"]');
            if (mobileInput && mobileInput.value) {
                if (!validateMobile(mobileInput.value)) {
                    event.preventDefault();
                    showFieldError(mobileInput, 'Invalid mobile number (09XX-XXX-XXXX)');
                }
            }
            
            form.classList.add('was-validated');
        }, false);
    });
}

/**
 * Auto-format inputs (TIN, Mobile)
 */
function initAutoFormat() {
    // TIN formatting
    const tinInputs = document.querySelectorAll('input[name="tin"]');
    tinInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 12) value = value.substr(0, 12);
            
            if (value.length > 9) {
                e.target.value = value.substr(0, 3) + '-' + value.substr(3, 3) + '-' + value.substr(6, 3) + '-' + value.substr(9);
            } else if (value.length > 6) {
                e.target.value = value.substr(0, 3) + '-' + value.substr(3, 3) + '-' + value.substr(6);
            } else if (value.length > 3) {
                e.target.value = value.substr(0, 3) + '-' + value.substr(3);
            } else {
                e.target.value = value;
            }
        });
    });
    
    // Mobile formatting
    const mobileInputs = document.querySelectorAll('input[name="mobile_number"]');
    mobileInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 11) value = value.substr(0, 11);
            
            if (value.length > 7) {
                e.target.value = value.substr(0, 4) + '-' + value.substr(4, 3) + '-' + value.substr(7);
            } else if (value.length > 4) {
                e.target.value = value.substr(0, 4) + '-' + value.substr(4);
            } else {
                e.target.value = value;
            }
        });
    });
}

/**
 * Validate Philippine TIN
 */
function validateTIN(tin) {
    const cleaned = tin.replace(/[^0-9]/g, '');
    return cleaned.length === 12 && /^\d+$/.test(cleaned);
}

/**
 * Validate Philippine Mobile Number
 */
function validateMobile(mobile) {
    const cleaned = mobile.replace(/[^0-9]/g, '');
    return cleaned.length === 11 && cleaned.startsWith('09');
}

/**
 * Show field error
 */
function showFieldError(input, message) {
    const feedback = input.nextElementSibling;
    if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = message;
    } else {
        const div = document.createElement('div');
        div.className = 'invalid-feedback';
        div.textContent = message;
        input.parentNode.appendChild(div);
    }
    input.classList.add('is-invalid');
}

/**
 * Deadline Countdown Updates
 */
function initCountdowns() {
    const countdownElements = document.querySelectorAll('[data-deadline]');
    
    function updateCountdowns() {
        countdownElements.forEach(element => {
            const deadline = new Date(element.dataset.deadline);
            const now = new Date();
            const diff = deadline - now;
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            
            if (days < 0) {
                element.textContent = `${Math.abs(days)} days overdue`;
                element.classList.add('text-danger');
            } else if (days === 0) {
                element.textContent = 'DUE TODAY';
                element.classList.add('text-warning');
            } else {
                element.textContent = `${days} day${days !== 1 ? 's' : ''} left`;
                if (days <= 3) {
                    element.classList.add('text-danger');
                } else if (days <= 7) {
                    element.classList.add('text-warning');
                } else {
                    element.classList.add('text-success');
                }
            }
        });
    }
    
    updateCountdowns();
    setInterval(updateCountdowns, 60000); // Update every minute
}

/**
 * Flash Messages Auto-dismiss
 */
function initFlashMessages() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

/**
 * File Upload Preview
 */
function initFileUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const preview = document.createElement('div');
            preview.className = 'alert alert-info mt-2 d-flex align-items-center';
            preview.innerHTML = `
                <i class="bi bi-file-earmark me-2"></i>
                <div class="flex-grow-1">
                    <strong>${file.name}</strong>
                    <small class="d-block text-muted">${(file.size / 1024).toFixed(2)} KB</small>
                </div>
            `;
            
            const existingPreview = input.parentNode.querySelector('.alert-info');
            if (existingPreview) {
                existingPreview.remove();
            }
            
            input.parentNode.appendChild(preview);
        });
    });
}

initFileUpload();

/**
 * Smooth Scroll
 */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

/**
 * Search functionality
 */
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const items = document.querySelectorAll('[data-searchable]');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(query) ? '' : 'none';
        });
    });
}

initSearch();

/**
 * Confirmation Dialogs
 */
document.querySelectorAll('[data-confirm]').forEach(element => {
    element.addEventListener('click', function(e) {
        const message = this.dataset.confirm || 'Are you sure?';
        if (!confirm(message)) {
            e.preventDefault();
        }
    });
});

/**
 * Copy to Clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!', 'success');
    });
}

/**
 * Show Toast Notification
 */
function showToast(message, type = 'info') {
    const toastHTML = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Export functions for use in other scripts
window.TaxMindr = {
    validateTIN,
    validateMobile,
    showToast,
    copyToClipboard
};
