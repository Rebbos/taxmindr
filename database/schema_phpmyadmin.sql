-- ==================================================
-- TaxMindr Database - Manual Installation
-- Copy and paste this into phpMyAdmin SQL tab
-- ==================================================

-- Step 1: Create and use database
CREATE DATABASE IF NOT EXISTS taxmindr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE taxmindr;

-- Step 2: Create tables

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    mobile_number VARCHAR(20),
    tin VARCHAR(20) UNIQUE,
    user_type ENUM('individual', 'business', 'freelancer', 'organization') NOT NULL,
    business_name VARCHAR(255),
    email_verified BOOLEAN DEFAULT FALSE,
    mobile_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    INDEX idx_email (email),
    INDEX idx_tin (tin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tax types reference
CREATE TABLE IF NOT EXISTS tax_types (
    tax_type_id INT AUTO_INCREMENT PRIMARY KEY,
    tax_code VARCHAR(50) UNIQUE NOT NULL,
    tax_name VARCHAR(255) NOT NULL,
    description TEXT,
    bir_form VARCHAR(50),
    filing_frequency ENUM('monthly', 'quarterly', 'annually', 'varies'),
    applicable_to SET('individual', 'business', 'freelancer', 'organization'),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tax deadlines
CREATE TABLE IF NOT EXISTS tax_deadlines (
    deadline_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tax_type_id INT NOT NULL,
    deadline_date DATE NOT NULL,
    filing_period VARCHAR(50),
    status ENUM('pending', 'filed', 'paid', 'late', 'exempted') DEFAULT 'pending',
    filed_date TIMESTAMP NULL,
    paid_date TIMESTAMP NULL,
    amount_due DECIMAL(15,2),
    penalty_amount DECIMAL(15,2) DEFAULT 0.00,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (tax_type_id) REFERENCES tax_types(tax_type_id),
    INDEX idx_user_deadline (user_id, deadline_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reminder settings
CREATE TABLE IF NOT EXISTS reminder_settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    days_before_deadline INT DEFAULT 7,
    email_enabled BOOLEAN DEFAULT TRUE,
    sms_enabled BOOLEAN DEFAULT FALSE,
    reminder_time TIME DEFAULT '09:00:00',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_setting (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reminder logs
CREATE TABLE IF NOT EXISTS reminder_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    deadline_id INT NOT NULL,
    reminder_type ENUM('email', 'sms') NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    error_message TEXT,
    FOREIGN KEY (deadline_id) REFERENCES tax_deadlines(deadline_id) ON DELETE CASCADE,
    INDEX idx_deadline (deadline_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BIR updates/announcements
CREATE TABLE IF NOT EXISTS tax_updates (
    update_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    summary TEXT NOT NULL,
    full_content TEXT,
    affected_taxpayers SET('individual', 'business', 'freelancer', 'organization'),
    action_required BOOLEAN DEFAULT FALSE,
    action_items TEXT,
    official_source_url VARCHAR(500),
    bir_memo_number VARCHAR(100),
    effective_date DATE,
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_published (published_at),
    INDEX idx_effective (effective_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Withholding lists/uploads
CREATE TABLE IF NOT EXISTS withholding_uploads (
    upload_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type ENUM('excel', 'csv') NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    filing_period VARCHAR(50),
    tax_type_id INT,
    total_records INT DEFAULT 0,
    valid_records INT DEFAULT 0,
    invalid_records INT DEFAULT 0,
    total_amount DECIMAL(15,2) DEFAULT 0.00,
    validation_status ENUM('pending', 'validated', 'has_errors', 'approved') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (tax_type_id) REFERENCES tax_types(tax_type_id),
    INDEX idx_user_period (user_id, filing_period)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Withholding records (parsed from uploads)
CREATE TABLE IF NOT EXISTS withholding_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    upload_id INT NOT NULL,
    line_number INT NOT NULL,
    payee_tin VARCHAR(20),
    payee_name VARCHAR(255),
    atc_code VARCHAR(10),
    tax_base DECIMAL(15,2),
    tax_rate DECIMAL(5,2),
    tax_withheld DECIMAL(15,2),
    has_error BOOLEAN DEFAULT FALSE,
    error_type ENUM('invalid_tin', 'missing_atc', 'wrong_total', 'missing_field', 'other'),
    error_message TEXT,
    is_corrected BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (upload_id) REFERENCES withholding_uploads(upload_id) ON DELETE CASCADE,
    INDEX idx_upload (upload_id),
    INDEX idx_errors (has_error, error_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Filing submissions
CREATE TABLE IF NOT EXISTS filing_submissions (
    submission_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    deadline_id INT,
    tax_type_id INT NOT NULL,
    filing_period VARCHAR(50) NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    amount_paid DECIMAL(15,2),
    receipt_image VARCHAR(500),
    screenshot_image VARCHAR(500),
    confirmation_number VARCHAR(100),
    notes TEXT,
    status ENUM('submitted', 'confirmed', 'rejected') DEFAULT 'submitted',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (deadline_id) REFERENCES tax_deadlines(deadline_id),
    FOREIGN KEY (tax_type_id) REFERENCES tax_types(tax_type_id),
    INDEX idx_user_submissions (user_id, submission_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity log
CREATE TABLE IF NOT EXISTS activity_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type VARCHAR(100) NOT NULL,
    description TEXT,
    related_table VARCHAR(100),
    related_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_activity (user_id, created_at),
    INDEX idx_activity_type (activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ATC (Alphanumeric Tax Code) Reference
CREATE TABLE IF NOT EXISTS atc_codes (
    atc_id INT AUTO_INCREMENT PRIMARY KEY,
    atc_code VARCHAR(10) UNIQUE NOT NULL,
    description TEXT NOT NULL,
    tax_rate DECIMAL(5,2) NOT NULL,
    category VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================================================
-- All tables created successfully!
-- Now you can run: install.php to populate sample data
-- ==================================================
