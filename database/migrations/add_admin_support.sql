-- Migration: Add Admin Support to TaxMindr
-- This script adds admin functionality to existing database

-- Step 1: Modify users table to add 'admin' to user_type ENUM
ALTER TABLE users 
MODIFY COLUMN user_type ENUM('admin', 'individual', 'business', 'freelancer', 'organization') NOT NULL;

-- Add index for user_type if not exists
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_user_type (user_type);

-- Step 2: Create admins table
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    role ENUM('super_admin', 'admin', 'moderator', 'support') DEFAULT 'admin',
    permissions JSON,
    can_manage_users BOOLEAN DEFAULT TRUE,
    can_manage_tax_updates BOOLEAN DEFAULT TRUE,
    can_view_analytics BOOLEAN DEFAULT TRUE,
    can_manage_system_settings BOOLEAN DEFAULT FALSE,
    department VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 3: Create admin_logs table
CREATE TABLE IF NOT EXISTS admin_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action_type ENUM('user_created', 'user_updated', 'user_deleted', 'user_suspended', 
                     'tax_update_posted', 'tax_update_edited', 'tax_update_deleted',
                     'system_setting_changed', 'report_generated', 'data_export', 'other') NOT NULL,
    description TEXT NOT NULL,
    affected_user_id INT NULL,
    related_table VARCHAR(100),
    related_id INT,
    old_value TEXT,
    new_value TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(admin_id) ON DELETE CASCADE,
    FOREIGN KEY (affected_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_admin_action (admin_id, created_at),
    INDEX idx_action_type (action_type),
    INDEX idx_affected_user (affected_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 4: Create system_settings table
CREATE TABLE IF NOT EXISTS system_settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 5: Create user_reports table
CREATE TABLE IF NOT EXISTS user_reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    report_type ENUM('bug', 'feature_request', 'complaint', 'inquiry', 'other') NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'resolved', 'closed') DEFAULT 'pending',
    assigned_to INT NULL,
    admin_notes TEXT,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES admins(admin_id) ON DELETE SET NULL,
    INDEX idx_user_report (user_id, created_at),
    INDEX idx_status (status),
    INDEX idx_assigned (assigned_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 6: Update tax_types applicable_to to include admin
ALTER TABLE tax_types 
MODIFY COLUMN applicable_to SET('admin', 'individual', 'business', 'freelancer', 'organization');

-- Step 7: Update tax_updates affected_taxpayers to include admin
ALTER TABLE tax_updates 
MODIFY COLUMN affected_taxpayers SET('admin', 'individual', 'business', 'freelancer', 'organization');

-- Step 8: Create default super admin (change password after first login!)
INSERT INTO users (email, password_hash, first_name, last_name, user_type, status)
VALUES ('admin@taxmindr.com', '$2y$10$YourHashedPasswordHere', 'System', 'Administrator', 'admin', 'active')
ON DUPLICATE KEY UPDATE user_type = 'admin';

-- Step 9: Create admin record for the super admin
-- Note: This assumes user_id = 1 is the admin. Adjust if needed.
INSERT INTO admins (user_id, role, can_manage_users, can_manage_tax_updates, can_view_analytics, can_manage_system_settings, department)
SELECT user_id, 'super_admin', TRUE, TRUE, TRUE, TRUE, 'System Administration'
FROM users 
WHERE email = 'admin@taxmindr.com'
ON DUPLICATE KEY UPDATE role = 'super_admin', can_manage_system_settings = TRUE;

-- Step 10: Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('app_name', 'TaxMindr', 'text', 'Application name', TRUE),
('maintenance_mode', 'false', 'boolean', 'Enable/disable maintenance mode', FALSE),
('max_upload_size', '10485760', 'number', 'Maximum file upload size in bytes (10MB)', FALSE),
('default_reminder_days', '7', 'number', 'Default days before deadline for reminders', TRUE),
('enable_email_notifications', 'true', 'boolean', 'Enable email notifications', FALSE),
('enable_sms_notifications', 'false', 'boolean', 'Enable SMS notifications', FALSE)
ON DUPLICATE KEY UPDATE setting_value = setting_value;

-- Migration complete!
-- Don't forget to:
-- 1. Update the admin password hash
-- 2. Test login with admin@taxmindr.com
-- 3. Verify admin dashboard access
