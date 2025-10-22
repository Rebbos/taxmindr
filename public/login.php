<?php
/**
 * User Login Page
 * TaxMindr - Philippine Tax Compliance Platform
 */

require_once '../config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $errors[] = 'Email and password are required';
    } else {
        try {
            $pdo = getDBConnection();
            
            // First, check in admins table
            $stmt = $pdo->prepare("
                SELECT admin_id as id, email, password_hash, first_name, last_name, role, status, 'admin' as account_type
                FROM admins 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();
            
            // If not found in admins, check in users table
            if (!$admin) {
                $stmt = $pdo->prepare("
                    SELECT user_id as id, email, password_hash, first_name, last_name, user_type, status, 'user' as account_type
                    FROM users 
                    WHERE email = ?
                ");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
            } else {
                $user = $admin;
            }
            
            if ($user && password_verify($password, $user['password_hash'])) {
                if ($user['status'] !== 'active') {
                    $errors[] = 'Account is inactive or suspended';
                } else {
                    // Set common session variables
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['account_type'] = $user['account_type'];
                    
                    if ($user['account_type'] === 'admin') {
                        // Admin login
                        $_SESSION['admin_id'] = $user['id'];
                        $_SESSION['admin_role'] = $user['role'];
                        $_SESSION['is_admin'] = true;
                        
                        // Update last login
                        $stmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE admin_id = ?");
                        $stmt->execute([$user['id']]);
                        
                        // Log admin activity
                        $stmt = $pdo->prepare("
                            INSERT INTO admin_logs (admin_id, action_type, description, ip_address, user_agent)
                            VALUES (?, 'other', 'Admin logged in', ?, ?)
                        ");
                        $stmt->execute([
                            $user['id'],
                            $_SERVER['REMOTE_ADDR'] ?? null,
                            $_SERVER['HTTP_USER_AGENT'] ?? null
                        ]);
                        
                        header('Location: ../admin/dashboard.php');
                    } else {
                        // Regular user login
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_type'] = $user['user_type'];
                        $_SESSION['is_admin'] = false;
                        
                        // Update last login
                        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
                        $stmt->execute([$user['id']]);
                        
                        // Log activity
                        logActivity($pdo, $user['id'], 'login', 'User logged in');
                        
                        header('Location: dashboard.php');
                    }
                    exit;
                }
            } else {
                $errors[] = 'Invalid email or password';
            }
        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            $errors[] = 'Login failed. Please try again.';
        }
    }
}

$pageTitle = 'Login - TaxMindr';
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
                <p>Login to your account</p>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember_me">
                        Remember me
                    </label>
                </div>
                
                <button type="submit" class="btn-primary btn-full">Login</button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <p><a href="index.php">Back to home</a></p>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>
