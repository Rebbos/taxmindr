<?php
/**
 * Create First Admin User
 * Run this script once to create your first admin account
 */

require_once '../config/config.php';

// Admin details - CHANGE THESE!
$adminEmail = 'admin@taxmindr.com';
$adminPassword = 'Admin@123'; // Change this to a secure password!
$firstName = 'System';
$lastName = 'Administrator';

try {
    $pdo = getDBConnection();
    
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "Admin user already exists with email: $adminEmail\n";
        echo "User ID: " . $existing['user_id'] . "\n";
        exit;
    }
    
    // Create admin user
    $passwordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password_hash, first_name, last_name, user_type, status, email_verified)
        VALUES (?, ?, ?, ?, 'admin', 'active', TRUE)
    ");
    
    $stmt->execute([$adminEmail, $passwordHash, $firstName, $lastName]);
    $userId = $pdo->lastInsertId();
    
    echo "✓ Admin user created successfully!\n";
    echo "User ID: $userId\n";
    echo "Email: $adminEmail\n";
    
    // Create admin record
    $stmt = $pdo->prepare("
        INSERT INTO admins (user_id, role, can_manage_users, can_manage_tax_updates, can_view_analytics, can_manage_system_settings, department)
        VALUES (?, 'super_admin', TRUE, TRUE, TRUE, TRUE, 'System Administration')
    ");
    
    $stmt->execute([$userId]);
    $adminId = $pdo->lastInsertId();
    
    echo "✓ Admin permissions set successfully!\n";
    echo "Admin ID: $adminId\n";
    echo "Role: Super Administrator\n\n";
    
    echo "===========================================\n";
    echo "LOGIN CREDENTIALS\n";
    echo "===========================================\n";
    echo "Email: $adminEmail\n";
    echo "Password: $adminPassword\n";
    echo "===========================================\n";
    echo "\nYou can now login at: " . APP_URL . "/public/login.php\n";
    echo "You will be redirected to admin dashboard.\n\n";
    echo "⚠️  IMPORTANT: Change the password after first login!\n";
    
} catch (PDOException $e) {
    echo "Error creating admin: " . $e->getMessage() . "\n";
    exit(1);
}
