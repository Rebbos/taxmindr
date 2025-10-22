<!-- Modern Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top shadow-sm">
    <div class="container-fluid px-4">
        <!-- Burger Menu for Sidebar Toggle (only show when logged in on pages with sidebar) -->
        <?php if (isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
        <button class="btn btn-link text-dark p-0 me-3" id="sidebarToggle" type="button" style="font-size: 1.5rem;">
            <i class="bi bi-list"></i>
        </button>
        <?php endif; ?>
        
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center" href="<?php echo APP_URL; ?>/public/index.php">
            <div class="brand-icon me-2">
                <i class="bi bi-calendar-check-fill text-primary"></i>
            </div>
            <span class="brand-text fw-bold">
                Tax<span class="text-primary">Mindr</span>
            </span>
        </a>
        
        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navigation Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/public/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/public/features.php">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/public/about.php">About</a>
                </li>
                
                <?php if (isLoggedIn()): ?>
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle me-2">
                                <span><?php echo strtoupper(substr($_SESSION['first_name'], 0, 1)); ?></span>
                            </div>
                            <span class="d-none d-lg-inline"><?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li>
                                <a class="dropdown-item" href="<?php echo APP_URL; ?>/public/dashboard.php">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo APP_URL; ?>/public/settings.php">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo APP_URL; ?>/public/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/public/login.php">Login</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary btn-sm px-4" href="<?php echo APP_URL; ?>/public/register.php">
                            Get Started
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
