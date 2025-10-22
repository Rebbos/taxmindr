<!-- Modern Sidebar -->
<div class="sidebar bg-white border-end shadow-sm" id="sidebar">
    <div class="sidebar-body p-3">
        <!-- User Info Card -->
        <div class="user-card card border-0 bg-light mb-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle avatar-lg me-3">
                        <span><?php echo strtoupper(substr($_SESSION['first_name'], 0, 1)); ?></span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h6>
                        <small class="text-muted text-capitalize">
                            <i class="bi bi-person-badge me-1"></i><?php echo $_SESSION['user_type']; ?>
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
                       href="<?php echo APP_URL; ?>/public/dashboard.php">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'deadlines.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/public/deadlines.php">
                        <i class="bi bi-calendar-event"></i>
                        <span>Tax Deadlines</span>
                        <?php
                        // Get pending count
                        if (isset($pdo)):
                            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tax_deadlines WHERE user_id = ? AND status = 'pending' AND deadline_date BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL 7 DAY)");
                            $stmt->execute([$_SESSION['user_id']]);
                            $upcomingCount = $stmt->fetch()['count'];
                            if ($upcomingCount > 0):
                        ?>
                        <span class="badge bg-warning text-dark ms-auto"><?php echo $upcomingCount; ?></span>
                        <?php endif; endif; ?>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'upload_withholding.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/public/upload_withholding.php">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        <span>Withholding Lists</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'updates.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/public/updates.php">
                        <i class="bi bi-newspaper"></i>
                        <span>Tax Updates</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'submissions.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/public/submissions.php">
                        <i class="bi bi-folder-check"></i>
                        <span>Filing Archive</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/public/reports.php">
                        <i class="bi bi-bar-chart-line"></i>
                        <span>Reports</span>
                    </a>
                </li>
            </ul>
            
            <hr class="my-3">
            
            <!-- Settings & Help -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>" 
                       href="<?php echo APP_URL; ?>/public/settings.php">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/public/help.php">
                        <i class="bi bi-question-circle"></i>
                        <span>Help & Support</span>
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
