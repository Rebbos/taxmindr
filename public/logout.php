<?php
/**
 * User Logout
 * TaxMindr - Philippine Tax Compliance Platform
 */

require_once '../config/config.php';

if (isLoggedIn()) {
    $pdo = getDBConnection();
    logActivity($pdo, $_SESSION['user_id'], 'logout', 'User logged out');
}

// Destroy session
session_destroy();

// Redirect to home
header('Location: index.php');
exit;
