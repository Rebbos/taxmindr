-- Migration: Add Admin Support to TaxMindr (Separate Tables Approach)
-- This script creates a separate admins table

-- Step 1: Create admins table (SEPARATE from users table)
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    mobile_number VARCHAR(20),
    role ENUM('super_admin', 'admin', 'moderator', 'support') DEFAULT 'admin',
    permissions JSON,
    can_manage_users BOOLEAN DEFAULT TRUE,
    can_manage_tax_updates BOOLEAN DEFAULT TRUE,
    can_view_analytics BOOLEAN DEFAULT TRUE,
    can_manage_system_settings BOOLEAN DEFAULT FALSE,
    department VARCHAR(100),
    notes TEXT,
    email_verified BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Create admin_logs table
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

-- Step 6: Remove user_type modifications (not needed with separate tables)
-- Users table stays as is for regular users only
-- Admins table is completely separate

-- Step 7: Create default super admin (change password after first login!)
INSERT INTO admins (email, password_hash, first_name, last_name, role, status, can_manage_system_settings, department)
VALUES ('admin@taxmindr.com', '$2y$10$YourHashedPasswordHere', 'System', 'Administrator', 'super_admin', 'active', TRUE, 'System Administration')
ON DUPLICATE KEY UPDATE role = 'super_admin', can_manage_system_settings = TRUE;

-- Step 8: Insert default system settings
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
