<?php
/**
 * User Dashboard
 * TaxMindr - Philippine Tax Compliance Platform
 */

require_once '../config/config.php';
requireLogin();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Get upcoming deadlines
$stmt = $pdo->prepare("
    SELECT d.*, t.tax_name, t.bir_form 
    FROM tax_deadlines d
    JOIN tax_types t ON d.tax_type_id = t.tax_type_id
    WHERE d.user_id = ? AND d.status = 'pending'
    ORDER BY d.deadline_date ASC
    LIMIT 5
");
$stmt->execute([$userId]);
$upcomingDeadlines = $stmt->fetchAll();

// Get recent tax updates
$stmt = $pdo->prepare("
    SELECT * FROM tax_updates 
    WHERE is_active = 1 
    AND (affected_taxpayers LIKE ? OR affected_taxpayers LIKE '%all%')
    ORDER BY published_at DESC 
    LIMIT 3
");
$stmt->execute(["%{$user['user_type']}%"]);
$taxUpdates = $stmt->fetchAll();

// Statistics
$stats = [];

// Pending deadlines count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tax_deadlines WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$userId]);
$stats['pending'] = $stmt->fetch()['count'];

// Filed this month
$stmt = $pdo->prepare("
    SELECT COUNT(*) as count FROM tax_deadlines 
    WHERE user_id = ? AND status = 'filed' 
    AND MONTH(filed_date) = MONTH(CURRENT_DATE())
    AND YEAR(filed_date) = YEAR(CURRENT_DATE())
");
$stmt->execute([$userId]);
$stats['filed'] = $stmt->fetch()['count'];

// Upcoming (within 7 days)
$stmt = $pdo->prepare("
    SELECT COUNT(*) as count FROM tax_deadlines 
    WHERE user_id = ? AND status = 'pending'
    AND deadline_date BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL 7 DAY)
");
$stmt->execute([$userId]);
$stats['upcoming'] = $stmt->fetch()['count'];

// Late/overdue
$stmt = $pdo->prepare("
    SELECT COUNT(*) as count FROM tax_deadlines 
    WHERE user_id = ? AND status = 'pending'
    AND deadline_date < CURRENT_DATE()
");
$stmt->execute([$userId]);
$stats['overdue'] = $stmt->fetch()['count'];

$pageTitle = 'Dashboard - TaxMindr';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="dashboard-main">
            <div class="dashboard-header">
                <h1>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! 👋</h1>
                <p>Here's your tax compliance overview</p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon pending">⏳</div>
                    <div class="stat-content">
                        <h3><?php echo $stats['pending']; ?></h3>
                        <p>Pending Deadlines</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon upcoming">⚠️</div>
                    <div class="stat-content">
                        <h3><?php echo $stats['upcoming']; ?></h3>
                        <p>Due Within 7 Days</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon filed">✅</div>
                    <div class="stat-content">
                        <h3><?php echo $stats['filed']; ?></h3>
                        <p>Filed This Month</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon overdue">❌</div>
                    <div class="stat-content">
                        <h3><?php echo $stats['overdue']; ?></h3>
                        <p>Overdue</p>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Grid -->
            <div class="dashboard-grid">
                <!-- Upcoming Deadlines -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>📅 Upcoming Deadlines</h2>
                        <a href="deadlines.php" class="link-button">View All</a>
                    </div>
                    <div class="card-content">
                        <?php if (empty($upcomingDeadlines)): ?>
                            <p class="empty-state">No upcoming deadlines. You're all caught up! 🎉</p>
                        <?php else: ?>
                            <div class="deadline-list">
                                <?php foreach ($upcomingDeadlines as $deadline): ?>
                                    <?php
                                    $daysLeft = daysUntilDeadline($deadline['deadline_date']);
                                    $urgencyClass = $daysLeft < 0 ? 'overdue' : ($daysLeft <= 3 ? 'urgent' : ($daysLeft <= 7 ? 'warning' : 'normal'));
                                    ?>
                                    <div class="deadline-item <?php echo $urgencyClass; ?>">
                                        <div class="deadline-info">
                                            <h4><?php echo htmlspecialchars($deadline['tax_name']); ?></h4>
                                            <p class="deadline-form"><?php echo htmlspecialchars($deadline['bir_form']); ?> - <?php echo htmlspecialchars($deadline['filing_period']); ?></p>
                                        </div>
                                        <div class="deadline-date">
                                            <span class="date"><?php echo formatDate($deadline['deadline_date'], 'M d, Y'); ?></span>
                                            <span class="days-left" data-deadline="<?php echo $deadline['deadline_date']; ?>">
                                                <?php 
                                                if ($daysLeft < 0) {
                                                    echo abs($daysLeft) . ' days overdue';
                                                } elseif ($daysLeft == 0) {
                                                    echo 'DUE TODAY';
                                                } else {
                                                    echo $daysLeft . ' day' . ($daysLeft != 1 ? 's' : '') . ' left';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <div class="deadline-actions">
                                            <a href="mark_filed.php?id=<?php echo $deadline['deadline_id']; ?>" class="btn-sm">Mark Filed</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Tax Updates -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>📰 Recent Tax Updates</h2>
                        <a href="updates.php" class="link-button">View All</a>
                    </div>
                    <div class="card-content">
                        <?php if (empty($taxUpdates)): ?>
                            <p class="empty-state">No recent updates</p>
                        <?php else: ?>
                            <div class="updates-list">
                                <?php foreach ($taxUpdates as $update): ?>
                                    <div class="update-item">
                                        <div class="update-header">
                                            <h4><?php echo htmlspecialchars($update['title']); ?></h4>
                                            <span class="update-date"><?php echo formatDate($update['published_at'], 'M d, Y'); ?></span>
                                        </div>
                                        <p><?php echo htmlspecialchars(substr($update['summary'], 0, 150)) . '...'; ?></p>
                                        <?php if ($update['action_required']): ?>
                                            <span class="badge badge-warning">Action Required</span>
                                        <?php endif; ?>
                                        <a href="update_details.php?id=<?php echo $update['update_id']; ?>" class="read-more">Read more →</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="upload_withholding.php" class="action-btn">
                        <span class="icon">📊</span>
                        <span class="text">Upload Withholding List</span>
                    </a>
                    <a href="deadlines.php" class="action-btn">
                        <span class="icon">📅</span>
                        <span class="text">View All Deadlines</span>
                    </a>
                    <a href="submissions.php" class="action-btn">
                        <span class="icon">📁</span>
                        <span class="text">Filing Archive</span>
                    </a>
                    <a href="settings.php" class="action-btn">
                        <span class="icon">⚙️</span>
                        <span class="text">Reminder Settings</span>
                    </a>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>
