<?php
/**
 * Admin Dashboard
 * TaxMindr - Philippine Tax Compliance Platform
 */

require_once '../config/config.php';
requireAdmin(); // Only admins can access

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];
$pageTitle = 'Admin Dashboard - TaxMindr';

// Get admin info
$adminInfo = getAdminPermissions($pdo, $userId);
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Get statistics
$stats = [];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE user_type != 'admin'");
$stats['total_users'] = $stmt->fetch()['count'];

// Active users (logged in last 30 days)
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND user_type != 'admin'");
$stats['active_users'] = $stmt->fetch()['count'];

// Total tax updates
$stmt = $pdo->query("SELECT COUNT(*) as count FROM tax_updates WHERE is_active = 1");
$stats['tax_updates'] = $stmt->fetch()['count'];

// Pending reports
$stmt = $pdo->query("SELECT COUNT(*) as count FROM user_reports WHERE status = 'pending'");
$stats['pending_reports'] = $stmt->fetch()['count'];

// Recent users
$stmt = $pdo->prepare("
    SELECT user_id, email, first_name, last_name, user_type, created_at, last_login, status
    FROM users 
    WHERE user_type != 'admin'
    ORDER BY created_at DESC 
    LIMIT 10
");
$stmt->execute();
$recentUsers = $stmt->fetchAll();

// Recent admin activity
$stmt = $pdo->prepare("
    SELECT al.*, u.first_name, u.last_name, au.first_name as affected_first_name, au.last_name as affected_last_name
    FROM admin_logs al
    JOIN admins a ON al.admin_id = a.admin_id
    JOIN users u ON a.user_id = u.user_id
    LEFT JOIN users au ON al.affected_user_id = au.user_id
    ORDER BY al.created_at DESC
    LIMIT 15
");
$stmt->execute();
$recentActivity = $stmt->fetchAll();

include '../components/head.php';
?>

<body>
    <!-- Decorative topbar accent line -->
    <?php include '../components/topbar.php'; ?>
    
    <div class="d-flex">
        <!-- Admin Sidebar -->
        <?php include '../components/admin-sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-wrapper flex-grow-1 d-flex flex-column">
            
            <?php
            $breadcrumbs = [
                ['label' => 'Admin Dashboard', 'url' => '#']
            ];
            include '../components/page-header.php';
            ?>
            
            <div class="flex-grow-1">
                <!-- Admin Header -->
                <div class="mb-4">
                    <h1 class="h3 fw-bold mb-2">
                        <i class="bi bi-shield-check me-2 text-danger"></i>Admin Dashboard
                    </h1>
                    <p class="text-muted mb-0">
                        Welcome, <?php echo htmlspecialchars($user['first_name']); ?>! 
                        <span class="badge bg-danger ms-2"><?php echo strtoupper(str_replace('_', ' ', $adminInfo['role'])); ?></span>
                    </p>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card stat-primary">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary-subtle me-3">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div>
                                    <h3 class="h2 mb-0 fw-bold"><?php echo $stats['total_users']; ?></h3>
                                    <p class="text-muted mb-0 small">Total Users</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card stat-success">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3" style="background: #d1fae5; color: #10b981;">
                                    <i class="bi bi-person-check"></i>
                                </div>
                                <div>
                                    <h3 class="h2 mb-0 fw-bold"><?php echo $stats['active_users']; ?></h3>
                                    <p class="text-muted mb-0 small">Active Users (30d)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card stat-warning">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3" style="background: #fef3c7; color: #f59e0b;">
                                    <i class="bi bi-newspaper"></i>
                                </div>
                                <div>
                                    <h3 class="h2 mb-0 fw-bold"><?php echo $stats['tax_updates']; ?></h3>
                                    <p class="text-muted mb-0 small">Tax Updates</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card stat-danger">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3" style="background: #fee2e2; color: #ef4444;">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h3 class="h2 mb-0 fw-bold"><?php echo $stats['pending_reports']; ?></h3>
                                    <p class="text-muted mb-0 small">Pending Reports</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Content Grid -->
                <div class="row g-4">
                    <!-- Recent Users -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Recent Users</h5>
                                <a href="users.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Type</th>
                                                <th>Registered</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentUsers as $rUser): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($rUser['first_name'] . ' ' . $rUser['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($rUser['email']); ?></td>
                                                <td><span class="badge bg-info"><?php echo ucfirst($rUser['user_type']); ?></span></td>
                                                <td><?php echo formatDate($rUser['created_at'], 'M d, Y'); ?></td>
                                                <td>
                                                    <?php if ($rUser['status'] === 'active'): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger"><?php echo ucfirst($rUser['status']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Admin Activity -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Activity</h5>
                            </div>
                            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                <?php if (empty($recentActivity)): ?>
                                    <p class="text-muted text-center py-3">No recent activity</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($recentActivity as $activity): ?>
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 small">
                                                        <strong><?php echo htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']); ?></strong>
                                                    </p>
                                                    <p class="mb-1 small text-muted">
                                                        <?php echo htmlspecialchars($activity['description']); ?>
                                                    </p>
                                                    <small class="text-muted">
                                                        <?php echo formatDate($activity['created_at'], 'M d, g:i A'); ?>
                                                    </small>
                                                </div>
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
            
            <?php include '../components/page-footer.php'; ?>
        </main>
    </div>
    
    <?php include '../components/foot.php'; ?>
</body>
</html>
