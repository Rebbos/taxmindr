<?php
/**
 * TaxMindr Installation Script
 * Run this once to set up the database
 */

require_once __DIR__ . '/../config/config.php';

echo "TaxMindr Installation Script\n";
echo "============================\n\n";

try {
    // Create database connection without selecting a database
    echo "Connecting to MySQL...\n";
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to MySQL\n\n";
    
    // Create database if it doesn't exist
    echo "Creating database '" . DB_NAME . "'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database created successfully\n\n";
    
    // Select the database
    $pdo->exec("USE `" . DB_NAME . "`");
    echo "Using database '" . DB_NAME . "'\n\n";
    
    // Read and execute schema.sql
    echo "Installing database schema...\n";
    $schemaFile = __DIR__ . '/../database/schema.sql';
    
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: $schemaFile");
    }
    
    $schema = file_get_contents($schemaFile);
    
    // Split into individual statements
    // Remove comments first
    $schema = preg_replace('/^--.*$/m', '', $schema);
    $schema = preg_replace('/\/\*.*?\*\//s', '', $schema);
    
    // Split by semicolon followed by newline
    $statements = explode(';', $schema);
    
    $executedCount = 0;
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                $executedCount++;
                // Show which table was created
                if (preg_match('/CREATE TABLE.*?`?(\w+)`?\s*\(/i', $statement, $matches)) {
                    echo "  ✓ Created table: {$matches[1]}\n";
                }
            } catch (PDOException $e) {
                echo "  ⚠ Warning: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "✓ Schema installed successfully ($executedCount statements executed)\n\n";
    
    // Insert sample tax types
    echo "Inserting sample data...\n";
    insertSampleData($pdo);
    echo "✓ Sample data inserted successfully\n\n";
    
    echo "============================\n";
    echo "✅ Installation completed successfully!\n\n";
    echo "Next steps:\n";
    echo "1. Access homepage: " . APP_URL . "/public/index.php\n";
    echo "2. Register account: " . APP_URL . "/public/register.php\n";
    echo "3. Test connection: " . APP_URL . "/scripts/test_connection.php\n";
    
} catch (PDOException $e) {
    echo "\n✗ Database Error: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting:\n";
    echo "1. Make sure MySQL is running in XAMPP Control Panel\n";
    echo "2. Check database credentials in config/database.php\n";
    echo "3. Default XAMPP: username='root', password='' (empty)\n";
    exit(1);
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

function insertSampleData($pdo) {
    // Sample tax types
    $taxTypes = [
        ['INCOME_TAX', 'Income Tax - Individual', 'Annual Income Tax Return', 'BIR Form 1701', 'annually', 'individual,freelancer'],
        ['INCOME_TAX_CORP', 'Income Tax - Corporation', 'Corporate Annual Income Tax Return', 'BIR Form 1702', 'annually', 'business,organization'],
        ['VAT', 'Value Added Tax', 'Monthly VAT Declaration', 'BIR Form 2550M', 'monthly', 'business'],
        ['PERCENTAGE_TAX', 'Percentage Tax', 'Monthly Percentage Tax Return', 'BIR Form 2551M', 'monthly', 'business,freelancer'],
        ['WITHHOLDING_EXPANDED', 'Expanded Withholding Tax', 'Monthly Remittance Return of Creditable Tax Withheld', 'BIR Form 1601E', 'monthly', 'business,organization'],
        ['WITHHOLDING_COMP', 'Withholding Tax - Compensation', 'Monthly Remittance Return of Income Taxes Withheld on Compensation', 'BIR Form 1601C', 'monthly', 'business,organization'],
        ['QUARTERLY_VAT', 'Quarterly VAT', 'Quarterly VAT Declaration', 'BIR Form 2550Q', 'quarterly', 'business'],
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO tax_types (tax_code, tax_name, description, bir_form, filing_frequency, applicable_to) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($taxTypes as $tax) {
        $stmt->execute($tax);
    }
    
    // Sample ATC codes
    $atcCodes = [
        ['WI010', 'Professional fees, talent fees, etc.', 10.00, 'Professional Services'],
        ['WI020', 'Professional fees paid to medical practitioners', 10.00, 'Medical Services'],
        ['WI030', 'Income payments to certain contractors', 2.00, 'Contractors'],
        ['WI040', 'Income payments made by credit card companies', 1.00, 'Credit Card'],
        ['WI050', 'Income payment on purchase of minerals, mineral products & quarry resources', 5.00, 'Mining'],
        ['WC010', 'Compensation income of minimum wage earners', 0.00, 'Minimum Wage'],
        ['WC020', 'Compensation income below 250K', 0.00, 'Low Income'],
        ['WC030', 'Compensation income exceeding 250K', 0.00, 'Regular Income'],
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO atc_codes (atc_code, description, tax_rate, category) 
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($atcCodes as $atc) {
        $stmt->execute($atc);
    }
}
