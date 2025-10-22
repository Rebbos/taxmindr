<?php
/**
 * Database Connection Test Script
 * TaxMindr - Philippine Tax Compliance Platform
 */

// Prevent direct access in production
if ($_SERVER['SERVER_NAME'] !== 'localhost' && $_SERVER['SERVER_NAME'] !== '127.0.0.1') {
    die('This script can only be run on localhost');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test - TaxMindr</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2563eb;
            margin-top: 0;
        }
        .success {
            padding: 15px;
            background: #d1fae5;
            color: #065f46;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #10b981;
        }
        .error {
            padding: 15px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #ef4444;
        }
        .info {
            padding: 15px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #2563eb;
        }
        .test-item {
            padding: 10px;
            margin: 10px 0;
            border-left: 3px solid #e5e7eb;
            padding-left: 15px;
        }
        .test-item.pass {
            border-left-color: #10b981;
        }
        .test-item.fail {
            border-left-color: #ef4444;
        }
        code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
        pre {
            background: #1f2937;
            color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔧 TaxMindr Database Connection Test</h1>
        <p>Testing your XAMPP MySQL connection and database setup...</p>
        
        <?php
        require_once __DIR__ . '/../config/database.php';
        
        $allTestsPassed = true;
        
        // Test 1: PHP Version
        echo '<h2>System Checks</h2>';
        echo '<div class="test-item ' . (version_compare(PHP_VERSION, '7.4.0', '>=') ? 'pass' : 'fail') . '">';
        echo '✓ PHP Version: ' . PHP_VERSION;
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            echo ' <strong>(PHP 7.4+ recommended)</strong>';
            $allTestsPassed = false;
        }
        echo '</div>';
        
        // Test 2: PDO Extension
        echo '<div class="test-item ' . (extension_loaded('pdo') ? 'pass' : 'fail') . '">';
        if (extension_loaded('pdo')) {
            echo '✓ PDO Extension: Loaded';
        } else {
            echo '✗ PDO Extension: Not loaded';
            $allTestsPassed = false;
        }
        echo '</div>';
        
        // Test 3: PDO MySQL Driver
        echo '<div class="test-item ' . (extension_loaded('pdo_mysql') ? 'pass' : 'fail') . '">';
        if (extension_loaded('pdo_mysql')) {
            echo '✓ PDO MySQL Driver: Loaded';
        } else {
            echo '✗ PDO MySQL Driver: Not loaded';
            $allTestsPassed = false;
        }
        echo '</div>';
        
        // Test 4: Database Connection
        echo '<h2>Database Connection</h2>';
        echo '<div class="info">';
        echo '<strong>Connection Details:</strong><br>';
        echo 'Host: ' . DB_HOST . '<br>';
        echo 'Database: ' . DB_NAME . '<br>';
        echo 'User: ' . DB_USER . '<br>';
        echo 'Charset: ' . DB_CHARSET;
        echo '</div>';
        
        try {
            $pdo = getDBConnection();
            echo '<div class="success">';
            echo '<strong>✓ Database Connection Successful!</strong><br>';
            echo 'Successfully connected to MySQL database.';
            echo '</div>';
            
            // Test 5: Check if tables exist
            echo '<h2>Database Tables</h2>';
            $tables = [
                'users',
                'tax_types',
                'tax_deadlines',
                'reminder_settings',
                'tax_updates',
                'withholding_uploads',
                'withholding_records',
                'filing_submissions',
                'activity_log',
                'atc_codes'
            ];
            
            $missingTables = [];
            foreach ($tables as $table) {
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                $exists = $stmt->rowCount() > 0;
                
                echo '<div class="test-item ' . ($exists ? 'pass' : 'fail') . '">';
                if ($exists) {
                    // Count records
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                    $count = $stmt->fetch()['count'];
                    echo "✓ Table <code>$table</code> exists ($count records)";
                } else {
                    echo "✗ Table <code>$table</code> is missing";
                    $missingTables[] = $table;
                    $allTestsPassed = false;
                }
                echo '</div>';
            }
            
            if (!empty($missingTables)) {
                echo '<div class="error">';
                echo '<strong>Missing Tables Detected!</strong><br>';
                echo 'Please run the installation script to create the database schema:<br>';
                echo '<a href="install.php">Run Installation Script</a>';
                echo '</div>';
            }
            
            // Test 6: Sample Data
            echo '<h2>Sample Data</h2>';
            
            // Check tax types
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM tax_types");
            $taxTypesCount = $stmt->fetch()['count'];
            
            echo '<div class="test-item ' . ($taxTypesCount > 0 ? 'pass' : 'fail') . '">';
            if ($taxTypesCount > 0) {
                echo "✓ Found $taxTypesCount tax types in database";
            } else {
                echo "⚠ No tax types found. Run installation script to populate sample data.";
            }
            echo '</div>';
            
            // Check ATC codes
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM atc_codes");
            $atcCount = $stmt->fetch()['count'];
            
            echo '<div class="test-item ' . ($atcCount > 0 ? 'pass' : 'fail') . '">';
            if ($atcCount > 0) {
                echo "✓ Found $atcCount ATC codes in database";
            } else {
                echo "⚠ No ATC codes found. Run installation script to populate sample data.";
            }
            echo '</div>';
            
        } catch (PDOException $e) {
            echo '<div class="error">';
            echo '<strong>✗ Database Connection Failed!</strong><br>';
            echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '<br><br>';
            
            echo '<strong>Troubleshooting Steps:</strong><br>';
            echo '1. Make sure MySQL is running in XAMPP Control Panel<br>';
            echo '2. Verify database credentials in <code>config/database.php</code><br>';
            echo '3. Check if database "' . DB_NAME . '" exists in phpMyAdmin<br>';
            echo '4. Default XAMPP settings: username=root, password=(empty)<br>';
            echo '</div>';
            $allTestsPassed = false;
        }
        
        // Final Summary
        echo '<h2>Summary</h2>';
        if ($allTestsPassed && isset($pdo)) {
            echo '<div class="success">';
            echo '<strong>🎉 All tests passed!</strong><br>';
            echo 'Your TaxMindr installation is ready to use.<br><br>';
            echo '<strong>Next Steps:</strong><br>';
            echo '1. <a href="../public/index.php">Go to Homepage</a><br>';
            echo '2. <a href="../public/register.php">Register an Account</a><br>';
            echo '3. <a href="../public/login.php">Login</a>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<strong>⚠ Some tests failed</strong><br>';
            echo 'Please review the errors above and fix them before proceeding.<br><br>';
            if (empty($missingTables) || !isset($pdo)) {
                echo '<a href="install.php" style="display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px;">Run Installation Script</a>';
            }
            echo '</div>';
        }
        ?>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">
        
        <h2>phpMyAdmin Access</h2>
        <div class="info">
            <strong>Access phpMyAdmin:</strong><br>
            <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a><br><br>
            <strong>Default Credentials:</strong><br>
            Username: root<br>
            Password: (leave empty)
        </div>
    </div>
</body>
</html>
