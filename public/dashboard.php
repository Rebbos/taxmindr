<?php
/**
 * User Dashboard - Modern Bootstrap 5 Design
 * TaxMindr - Philippine Tax Compliance Platform
 */

require_once '../config/config.php';
requireLogin();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];
$pageTitle = 'Dashboard - TaxMindr';

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
            <!-- Page Header -->
            <div class="mb-4 fade-in-up">
                <h1 class="h3 fw-bold mb-2">
                    Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! 
                    <span class="wave">👋</span>
                </h1>
                <p class="text-muted mb-0">Here's your tax compliance overview</p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-primary">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary-subtle me-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <h3 class="h2 mb-0 fw-bold"><?php echo $stats['pending']; ?></h3>
                                <p class="text-muted mb-0 small">Pending Deadlines</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-warning">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon me-3" style="background: #fef3c7; color: #f59e0b;">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h3 class="h2 mb-0 fw-bold"><?php echo $stats['upcoming']; ?></h3>
                                <p class="text-muted mb-0 small">Due Within 7 Days</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-success">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon me-3" style="background: #d1fae5; color: #10b981;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <h3 class="h2 mb-0 fw-bold"><?php echo $stats['filed']; ?></h3>
                                <p class="text-muted mb-0 small">Filed This Month</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-danger">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon me-3" style="background: #fee2e2; color: #ef4444;">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div>
                                <h3 class="h2 mb-0 fw-bold"><?php echo $stats['overdue']; ?></h3>
                                <p class="text-muted mb-0 small">Overdue</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Grid -->
            <div class="row g-4">
                <!-- Upcoming Deadlines -->
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-event me-2"></i>Upcoming Deadlines
                            </h5>
                            <a href="deadlines.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($upcomingDeadlines)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-3 mb-0">No upcoming deadlines. You're all caught up! 🎉</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($upcomingDeadlines as $deadline): ?>
                                        <?php
                                        $daysLeft = daysUntilDeadline($deadline['deadline_date']);
                                        $badgeClass = $daysLeft < 0 ? 'bg-danger' : ($daysLeft <= 3 ? 'bg-danger' : ($daysLeft <= 7 ? 'bg-warning' : 'bg-success'));
                                        ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-semibold"><?php echo htmlspecialchars($deadline['tax_name']); ?></h6>
                                                    <p class="mb-1 small text-muted">
                                                        <i class="bi bi-file-text me-1"></i><?php echo htmlspecialchars($deadline['bir_form']); ?> - 
                                                        <?php echo htmlspecialchars($deadline['filing_period']); ?>
                                                    </p>
                                                    <div class="d-flex align-items-center gap-2 mt-2">
                                                        <span class="badge <?php echo $badgeClass; ?>" data-deadline="<?php echo $deadline['deadline_date']; ?>">
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
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar me-1"></i><?php echo formatDate($deadline['deadline_date'], 'M d, Y'); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <a href="mark_filed.php?id=<?php echo $deadline['deadline_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-check2"></i> Mark Filed
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Tax Updates -->
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-newspaper me-2"></i>Tax Updates
                            </h5>
                            <a href="updates.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($taxUpdates)): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-info-circle text-muted" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted mt-3 mb-0 small">No recent updates</p>
                                </div>
                            <?php else: ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($taxUpdates as $update): ?>
                                        <div class="border-start border-primary border-3 ps-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($update['title']); ?></h6>
                                                <?php if ($update['action_required']): ?>
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="small text-muted mb-2"><?php echo htmlspecialchars(substr($update['summary'], 0, 100)) . '...'; ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i><?php echo formatDate($update['published_at'], 'M d'); ?>
                                                </small>
                                                <a href="update_details.php?id=<?php echo $update['update_id']; ?>" class="btn btn-link btn-sm p-0">
                                                    Read more <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                    </div>
                </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Include modern footer -->
    <?php include '../components/foot.php'; ?>
