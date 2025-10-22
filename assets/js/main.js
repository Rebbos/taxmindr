/**
 * TaxMindr - Main JavaScript
 * Philippine Tax Compliance Platform
 */

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    console.log('TaxMindr initialized');
    
    // Initialize components
    initFlashMessages();
    initFormValidation();
    initDateInputs();
});

/**
 * Flash Messages
 */
function initFlashMessages() {
    const flashMessage = document.querySelector('.flash-message');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.opacity = '0';
            setTimeout(() => flashMessage.remove(), 300);
        }, 5000);
    }
}

/**
 * Form Validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
}

function validateForm(form) {
    let isValid = true;
    
    // TIN validation
    const tinInput = form.querySelector('input[name="tin"]');
    if (tinInput && tinInput.value) {
        if (!validateTIN(tinInput.value)) {
            showError(tinInput, 'Invalid TIN format. Use XXX-XXX-XXX-XXX');
            isValid = false;
        } else {
            clearError(tinInput);
        }
    }
    
    // Mobile validation
    const mobileInput = form.querySelector('input[name="mobile_number"]');
    if (mobileInput && mobileInput.value) {
        if (!validateMobile(mobileInput.value)) {
            showError(mobileInput, 'Invalid mobile number. Use 09XX-XXX-XXXX');
            isValid = false;
        } else {
            clearError(mobileInput);
        }
    }
    
    // Email validation
    const emailInput = form.querySelector('input[type="email"]');
    if (emailInput && emailInput.value) {
        if (!validateEmail(emailInput.value)) {
            showError(emailInput, 'Invalid email address');
            isValid = false;
        } else {
            clearError(emailInput);
        }
    }
    
    // Password strength
    const passwordInput = form.querySelector('input[name="password"]');
    if (passwordInput && passwordInput.value) {
        if (passwordInput.value.length < 8) {
            showError(passwordInput, 'Password must be at least 8 characters');
            isValid = false;
        } else {
            clearError(passwordInput);
        }
    }
    
    return isValid;
}

/**
 * Validation Functions
 */
function validateTIN(tin) {
    const cleaned = tin.replace(/[^0-9]/g, '');
    return cleaned.length === 12 && /^\d+$/.test(cleaned);
}

function validateMobile(mobile) {
    const cleaned = mobile.replace(/[^0-9]/g, '');
    return cleaned.length === 11 && cleaned.startsWith('09');
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Error Display
 */
function showError(input, message) {
    clearError(input);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.color = '#ef4444';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    
    input.style.borderColor = '#ef4444';
    input.parentNode.appendChild(errorDiv);
}

function clearError(input) {
    input.style.borderColor = '';
    const errorDiv = input.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * Auto-format inputs
 */
function initDateInputs() {
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
 * Deadline countdown
 */
function updateDeadlineCountdown() {
    const countdowns = document.querySelectorAll('[data-deadline]');
    
    countdowns.forEach(element => {
        const deadline = new Date(element.dataset.deadline);
        const now = new Date();
        const diff = deadline - now;
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        
        if (days < 0) {
            element.textContent = 'OVERDUE';
            element.style.color = '#ef4444';
        } else if (days === 0) {
            element.textContent = 'DUE TODAY';
            element.style.color = '#f59e0b';
        } else {
            element.textContent = `${days} day${days !== 1 ? 's' : ''} left`;
            element.style.color = days <= 7 ? '#f59e0b' : '#10b981';
        }
    });
}

// Update countdowns every minute
setInterval(updateDeadlineCountdown, 60000);
updateDeadlineCountdown();

/**
 * File upload preview
 */
function initFileUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const preview = document.createElement('div');
                preview.className = 'file-preview';
                preview.innerHTML = `
                    <span>${file.name}</span>
                    <span>${(file.size / 1024).toFixed(2)} KB</span>
                `;
                
                const existingPreview = input.parentNode.querySelector('.file-preview');
                if (existingPreview) {
                    existingPreview.remove();
                }
                
                input.parentNode.appendChild(preview);
            }
        });
    });
}

initFileUpload();
