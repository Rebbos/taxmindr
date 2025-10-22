<?php
/**
 * User Settings
 * TaxMindr - Philippine Tax Compliance Platform
 */

require_once '../config/config.php';
requireLogin();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

$errors = [];
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $firstName = sanitize($_POST['first_name'] ?? '');
        $lastName = sanitize($_POST['last_name'] ?? '');
        $mobileNumber = sanitize($_POST['mobile_number'] ?? '');
        $tin = sanitize($_POST['tin'] ?? '');
        $businessName = sanitize($_POST['business_name'] ?? '');
        
        if (empty($firstName) || empty($lastName)) {
            $errors[] = 'First name and last name are required';
        } else {
            try {
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET first_name = ?, last_name = ?, mobile_number = ?, tin = ?, business_name = ?
                    WHERE user_id = ?
                ");
                
                $cleanTin = preg_replace('/[^0-9]/', '', $tin);
                
                $stmt->execute([
                    $firstName,
                    $lastName,
                    $mobileNumber ?: null,
                    $cleanTin ?: null,
                    $businessName ?: null,
                    $userId
                ]);
                
                $_SESSION['first_name'] = $firstName;
                $_SESSION['last_name'] = $lastName;
                
                logActivity($pdo, $userId, 'profile_update', 'Profile information updated');
                $success = 'Profile updated successfully!';
            } catch (PDOException $e) {
                error_log("Profile Update Error: " . $e->getMessage());
                $errors[] = 'Failed to update profile';
            }
        }
    } elseif ($action === 'update_reminders') {
        $daysBeforeDeadline = (int)($_POST['days_before_deadline'] ?? 7);
        $emailEnabled = isset($_POST['email_enabled']) ? 1 : 0;
        $smsEnabled = isset($_POST['sms_enabled']) ? 1 : 0;
        $reminderTime = sanitize($_POST['reminder_time'] ?? '09:00:00');
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO reminder_settings (user_id, days_before_deadline, email_enabled, sms_enabled, reminder_time)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    days_before_deadline = VALUES(days_before_deadline),
                    email_enabled = VALUES(email_enabled),
                    sms_enabled = VALUES(sms_enabled),
                    reminder_time = VALUES(reminder_time)
            ");
            
            $stmt->execute([$userId, $daysBeforeDeadline, $emailEnabled, $smsEnabled, $reminderTime]);
            
            logActivity($pdo, $userId, 'settings_update', 'Reminder settings updated');
            $success = 'Reminder settings updated successfully!';
        } catch (PDOException $e) {
            error_log("Reminder Update Error: " . $e->getMessage());
            $errors[] = 'Failed to update reminder settings';
        }
    }
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Get reminder settings
$stmt = $pdo->prepare("SELECT * FROM reminder_settings WHERE user_id = ?");
$stmt->execute([$userId]);
$reminderSettings = $stmt->fetch();

if (!$reminderSettings) {
    $reminderSettings = [
        'days_before_deadline' => DEFAULT_REMINDER_DAYS,
        'email_enabled' => 1,
        'sms_enabled' => 0,
        'reminder_time' => '09:00:00'
    ];
}

$pageTitle = 'Settings - TaxMindr';

// Include modern head
include '../components/head.php';
?>

<body>
    <!-- Include modern navbar -->
    <?php include '../components/navbar.php'; ?>
    
    <div class="d-flex">
        <!-- Include modern sidebar -->
        <?php include '../components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-wrapper flex-grow-1">
            <div class="page-header">
                <div>
                    <h1>⚙️ Settings</h1>
                    <p>Manage your account and preferences</p>
                </div>
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
            
            <div class="settings-container">
                <!-- Profile Settings -->
                <div class="settings-card">
                    <h2>👤 Profile Information</h2>
                    
                    <form method="POST" action="" data-validate>
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" required
                                       value="<?php echo htmlspecialchars($user['first_name']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" required
                                       value="<?php echo htmlspecialchars($user['last_name']); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <small>Email cannot be changed</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="mobile_number">Mobile Number</label>
                            <input type="text" id="mobile_number" name="mobile_number"
                                   value="<?php echo htmlspecialchars($user['mobile_number'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="tin">TIN</label>
                            <input type="text" id="tin" name="tin"
                                   value="<?php echo $user['tin'] ? formatTIN($user['tin']) : ''; ?>">
                        </div>
                        
                        <?php if ($user['user_type'] === 'business' || $user['user_type'] === 'organization'): ?>
                            <div class="form-group">
                                <label for="business_name">Business/Organization Name</label>
                                <input type="text" id="business_name" name="business_name"
                                       value="<?php echo htmlspecialchars($user['business_name'] ?? ''); ?>">
                            </div>
                        <?php endif; ?>
                        
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </form>
                </div>
                
                <!-- Reminder Settings -->
                <div class="settings-card">
                    <h2>🔔 Reminder Settings</h2>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_reminders">
                        
                        <div class="form-group">
                            <label for="days_before_deadline">Remind me before deadline</label>
                            <select id="days_before_deadline" name="days_before_deadline">
                                <?php for ($i = 1; $i <= 30; $i++): ?>
                                    <option value="<?php echo $i; ?>" 
                                        <?php echo $reminderSettings['days_before_deadline'] == $i ? 'selected' : ''; ?>>
                                        <?php echo $i; ?> day<?php echo $i > 1 ? 's' : ''; ?> before
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="reminder_time">Preferred reminder time</label>
                            <input type="time" id="reminder_time" name="reminder_time"
                                   value="<?php echo htmlspecialchars($reminderSettings['reminder_time']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="email_enabled" 
                                       <?php echo $reminderSettings['email_enabled'] ? 'checked' : ''; ?>>
                                Send email reminders
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="sms_enabled"
                                       <?php echo $reminderSettings['sms_enabled'] ? 'checked' : ''; ?>>
                                Send SMS reminders
                            </label>
                            <small>Requires valid mobile number</small>
                        </div>
                        
                        <button type="submit" class="btn-primary">Save Reminder Settings</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/main.js"></script>
    
    <style>
        .settings-container {
            display: grid;
            gap: 1.5rem;
        }
        
        .settings-card {
            background: var(--bg-white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow-sm);
        }
        
        .settings-card h2 {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
    </style>
    
    <!-- Include modern footer -->
    <?php include '../components/foot.php'; ?>
