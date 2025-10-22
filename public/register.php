<?php
/**
 * User Registration Page
 * TaxMindr - Philippine Tax Compliance Platform
 */

require_once '../config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $firstName = sanitize($_POST['first_name'] ?? '');
    $lastName = sanitize($_POST['last_name'] ?? '');
    $mobileNumber = sanitize($_POST['mobile_number'] ?? '');
    $tin = sanitize($_POST['tin'] ?? '');
    $userType = sanitize($_POST['user_type'] ?? '');
    $businessName = sanitize($_POST['business_name'] ?? '');
    
    // Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($firstName) || empty($lastName)) {
        $errors[] = 'First name and last name are required';
    }
    
    if (!empty($mobileNumber) && !validateMobile($mobileNumber)) {
        $errors[] = 'Invalid mobile number format';
    }
    
    if (!empty($tin) && !validateTIN($tin)) {
        $errors[] = 'Invalid TIN format';
    }
    
    if (empty($userType)) {
        $errors[] = 'User type is required';
    }
    
    // If no errors, create user
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            
            // Check if email exists
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email already registered';
            } else {
                // Create user
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $cleanTin = preg_replace('/[^0-9]/', '', $tin);
                
                $stmt = $pdo->prepare("
                    INSERT INTO users (email, password_hash, first_name, last_name, mobile_number, tin, user_type, business_name)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $email,
                    $passwordHash,
                    $firstName,
                    $lastName,
                    $mobileNumber ?: null,
                    $cleanTin ?: null,
                    $userType,
                    $businessName ?: null
                ]);
                
                $userId = $pdo->lastInsertId();
                
                // Create default reminder settings
                $stmt = $pdo->prepare("
                    INSERT INTO reminder_settings (user_id, days_before_deadline, email_enabled)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$userId, DEFAULT_REMINDER_DAYS, 1]);
                
                // Log activity
                logActivity($pdo, $userId, 'registration', 'New user registered');
                
                $success = 'Registration successful! Please login.';
                
                // Redirect to login after 2 seconds
                header('Refresh: 2; url=login.php');
            }
        } catch (PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}

$pageTitle = 'Register - TaxMindr';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>TaxMindr</h1>
                <p>Create your account</p>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <p><?php echo $success; ?></p>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" data-validate>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required 
                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" id="mobile_number" name="mobile_number" 
                           placeholder="09XX-XXX-XXXX"
                           value="<?php echo htmlspecialchars($_POST['mobile_number'] ?? ''); ?>">
                    <small>Optional, but required for SMS reminders</small>
                </div>
                
                <div class="form-group">
                    <label for="tin">TIN (Tax Identification Number)</label>
                    <input type="text" id="tin" name="tin" 
                           placeholder="XXX-XXX-XXX-XXX"
                           value="<?php echo htmlspecialchars($_POST['tin'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="user_type">I am a *</label>
                    <select id="user_type" name="user_type" required>
                        <option value="">Select type</option>
                        <option value="individual" <?php echo ($_POST['user_type'] ?? '') === 'individual' ? 'selected' : ''; ?>>Individual Taxpayer</option>
                        <option value="freelancer" <?php echo ($_POST['user_type'] ?? '') === 'freelancer' ? 'selected' : ''; ?>>Freelancer</option>
                        <option value="business" <?php echo ($_POST['user_type'] ?? '') === 'business' ? 'selected' : ''; ?>>Business Owner</option>
                        <option value="organization" <?php echo ($_POST['user_type'] ?? '') === 'organization' ? 'selected' : ''; ?>>Organization/Church</option>
                    </select>
                </div>
                
                <div class="form-group" id="business_name_group" style="display: none;">
                    <label for="business_name">Business/Organization Name</label>
                    <input type="text" id="business_name" name="business_name"
                           value="<?php echo htmlspecialchars($_POST['business_name'] ?? ''); ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required>
                        <small>Minimum 8 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary btn-full">Create Account</button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
                <p><a href="index.php">Back to home</a></p>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
    <script>
        // Show/hide business name field
        document.getElementById('user_type').addEventListener('change', function() {
            const businessGroup = document.getElementById('business_name_group');
            if (this.value === 'business' || this.value === 'organization') {
                businessGroup.style.display = 'block';
            } else {
                businessGroup.style.display = 'none';
            }
        });
        
        // Trigger on page load
        document.getElementById('user_type').dispatchEvent(new Event('change'));
    </script>
</body>
</html>
