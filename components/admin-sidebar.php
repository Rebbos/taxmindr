<!-- Admin Sidebar -->
<div class="sidebar bg-white border-end shadow-sm" id="sidebar">
    <div class="sidebar-body p-3">
        <!-- Admin Info Card -->
        <div class="user-card card border-0 bg-danger bg-opacity-10 mb-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle avatar-lg me-3" style="background: linear-gradient(135deg, #dc3545, #c82333);">
                        <span><?php echo strtoupper(substr($_SESSION['first_name'], 0, 1)); ?></span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h6>
                        <small class="text-danger">
                            <i class="bi bi-shield-check me-1"></i>Administrator
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/admin/dashboard.php">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/admin/users.php">
                        <i class="bi bi-people"></i>
                        <span>Manage Users</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'tax-updates.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/admin/tax-updates.php">
                        <i class="bi bi-newspaper"></i>
                        <span>Tax Updates</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/admin/reports.php">
                        <i class="bi bi-flag"></i>
                        <span>User Reports</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'analytics.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/admin/analytics.php">
                        <i class="bi bi-graph-up"></i>
                        <span>Analytics</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'activity-logs.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/admin/activity-logs.php">
                        <i class="bi bi-clock-history"></i>
                        <span>Activity Logs</span>
                    </a>
                </li>
                
                <?php if (isset($adminInfo) && $adminInfo['can_manage_system_settings']): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/admin/settings.php">
                        <i class="bi bi-gear"></i>
                        <span>System Settings</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            
            <hr class="my-3">
            
            <!-- Quick Actions -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-primary" href="<?php echo APP_URL; ?>/public/dashboard.php">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>User View</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/admin/help.php">
                        <i class="bi bi-question-circle"></i>
                        <span>Help</span>
                    </a>
                </li>
                
                <li class="nav-item mt-3">
                    <a class="nav-link text-danger" href="<?php echo APP_URL; ?>/public/logout.php">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Sidebar Overlay (for mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
