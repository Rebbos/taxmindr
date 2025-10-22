<?php
/**
 * Helper Functions
 * TaxMindr - Philippine Tax Compliance Platform
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate Philippine TIN format
 * Format: XXX-XXX-XXX-XXX or XXXXXXXXXXXX (12 digits)
 */
function validateTIN($tin) {
    $tin = preg_replace('/[^0-9]/', '', $tin);
    return strlen($tin) === 12 && ctype_digit($tin);
}

/**
 * Format TIN for display
 */
function formatTIN($tin) {
    $tin = preg_replace('/[^0-9]/', '', $tin);
    if (strlen($tin) === 12) {
        return substr($tin, 0, 3) . '-' . substr($tin, 3, 3) . '-' . substr($tin, 6, 3) . '-' . substr($tin, 9, 3);
    }
    return $tin;
}

/**
 * Validate Philippine mobile number
 * Format: 09XX-XXX-XXXX or +639XXXXXXXXX
 */
function validateMobile($mobile) {
    $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
    // Remove country code if present
    if (substr($mobile, 0, 2) === '63') {
        $mobile = '0' . substr($mobile, 2);
    }
    
    return strlen($mobile) === 11 && substr($mobile, 0, 2) === '09';
}

/**
 * Format mobile number
 */
function formatMobile($mobile) {
    $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
    if (strlen($mobile) === 11) {
        return substr($mobile, 0, 4) . '-' . substr($mobile, 4, 3) . '-' . substr($mobile, 7, 4);
    }
    return $mobile;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if logged-in user is an admin
 */
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/public/login.php');
        exit;
    }
}

/**
 * Redirect to admin dashboard if not admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . APP_URL . '/public/dashboard.php');
        exit;
    }
}

/**
 * Get admin permissions
 */
function getAdminPermissions($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/**
 * Log admin activity
 */
function logAdminActivity($pdo, $adminId, $actionType, $description, $affectedUserId = null, $relatedTable = null, $relatedId = null, $oldValue = null, $newValue = null) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_logs 
            (admin_id, action_type, description, affected_user_id, related_table, related_id, old_value, new_value, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $adminId,
            $actionType,
            $description,
            $affectedUserId,
            $relatedTable,
            $relatedId,
            $oldValue,
            $newValue,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Admin Activity Log Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Log user activity
 */
function logActivity($pdo, $userId, $activityType, $description, $relatedTable = null, $relatedId = null) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_log 
            (user_id, activity_type, description, related_table, related_id, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $userId,
            $activityType,
            $description,
            $relatedTable,
            $relatedId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Activity Log Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Format currency (Philippine Peso)
 */
function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}

/**
 * Format date to Philippine format
 */
function formatDate($date, $format = 'F d, Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Calculate days until deadline
 */
function daysUntilDeadline($deadline) {
    $now = new DateTime();
    $deadlineDate = new DateTime($deadline);
    $interval = $now->diff($deadlineDate);
    
    return $interval->invert ? -$interval->days : $interval->days;
}

/**
 * Check if deadline is approaching (within reminder days)
 */
function isDeadlineApproaching($deadline, $reminderDays = 7) {
    $daysLeft = daysUntilDeadline($deadline);
    return $daysLeft >= 0 && $daysLeft <= $reminderDays;
}

/**
 * Generate secure random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Validate file upload
 */
function validateFileUpload($file, $allowedTypes = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds limit'];
    }
    
    $allowedTypes = $allowedTypes ?? ALLOWED_FILE_TYPES;
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedTypes)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    return ['success' => true, 'extension' => $extension];
}

/**
 * Send email notification
 */
function sendEmail($to, $subject, $body) {
    // Implement with PHPMailer or similar
    // This is a placeholder
    error_log("Email to $to: $subject");
    return true;
}

/**
 * Send SMS notification
 */
function sendSMS($mobile, $message) {
    // Implement with Semaphore, Twilio, or similar
    // This is a placeholder
    error_log("SMS to $mobile: $message");
    return true;
}

/**
 * Get user's full name
 */
function getUserFullName($userId, $pdo) {
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    return $user ? $user['first_name'] . ' ' . $user['last_name'] : 'Unknown';
}

/**
 * Flash message helpers
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}
