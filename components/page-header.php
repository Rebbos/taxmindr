<!-- Internal Page Header (inside main content area) -->
<div class="page-header bg-white border-bottom shadow-sm">
    <div class="px-4 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Left: Burger + Breadcrumb -->
            <div class="d-flex align-items-center gap-3">
                <!-- Burger Menu Toggle -->
                <button class="btn btn-link text-dark p-0 d-flex align-items-center justify-content-center" 
                        id="sidebarToggle" 
                        type="button" 
                        style="font-size: 1.75rem; line-height: 1; width: 40px; height: 40px;">
                    <i class="bi bi-list"></i>
                </button>
                
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                            <?php foreach ($breadcrumbs as $key => $crumb): ?>
                                <?php if ($key === array_key_last($breadcrumbs)): ?>
                                    <li class="breadcrumb-item active fw-semibold" aria-current="page">
                                        <?php echo htmlspecialchars($crumb['label']); ?>
                                    </li>
                                <?php else: ?>
                                    <li class="breadcrumb-item">
                                        <a href="<?php echo htmlspecialchars($crumb['url']); ?>">
                                            <?php echo htmlspecialchars($crumb['label']); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
            
            <!-- Right: Page Actions + Notifications + User -->
            <div class="d-flex align-items-center gap-3">
                <!-- Page Actions (optional) -->
                <?php if (isset($pageActions) && !empty($pageActions)): ?>
                    <div class="d-flex gap-2">
                        <?php foreach ($pageActions as $action): ?>
                            <a href="<?php echo htmlspecialchars($action['url']); ?>" 
                               class="btn btn-<?php echo $action['style'] ?? 'primary'; ?> btn-sm">
                                <?php if (isset($action['icon'])): ?>
                                    <i class="bi bi-<?php echo $action['icon']; ?> me-1"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($action['label']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Notifications Bell -->
                <div class="position-relative">
                    <a href="#" class="btn btn-link text-dark p-0 position-relative" 
                       id="notificationBell" 
                       data-bs-toggle="dropdown" 
                       style="font-size: 1.5rem;">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              style="font-size: 0.65rem;">
                            3
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    </a>
                    <!-- Notifications Dropdown -->
                    <ul class="dropdown-menu dropdown-menu-end shadow p-0" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center bg-light py-2 px-3">
                            <span class="fw-bold">Notifications</span>
                            <a href="#" class="small text-primary">Mark all read</a>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li>
                            <a class="dropdown-item py-3 px-3" href="#">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-exclamation-circle text-warning fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-1 small fw-semibold">Deadline Approaching</p>
                                        <p class="mb-1 small text-muted">Income Tax filing due in 3 days</p>
                                        <small class="text-muted">2 hours ago</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li>
                            <a class="dropdown-item py-3 px-3" href="#">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-info-circle text-info fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-1 small fw-semibold">New Tax Update</p>
                                        <p class="mb-1 small text-muted">BIR released new guidelines</p>
                                        <small class="text-muted">1 day ago</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li>
                            <a class="dropdown-item py-3 px-3" href="#">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-check-circle text-success fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-1 small fw-semibold">Upload Successful</p>
                                        <p class="mb-1 small text-muted">Withholding list processed</p>
                                        <small class="text-muted">2 days ago</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li class="text-center py-2">
                            <a href="#" class="small text-primary">View all notifications</a>
                        </li>
                    </ul>
                </div>
                
                <!-- User Dropdown -->
                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle" 
                       href="#" 
                       id="userDropdown" 
                       role="button" 
                       data-bs-toggle="dropdown">
                        <div class="avatar-circle me-2">
                            <span><?php echo strtoupper(substr($_SESSION['first_name'], 0, 1)); ?></span>
                        </div>
                        <span class="d-none d-lg-inline fw-semibold">
                            <?php echo htmlspecialchars($_SESSION['first_name']); ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li class="px-3 py-2">
                            <div class="small text-muted">Signed in as</div>
                            <div class="fw-semibold">
                                <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
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
                </div>
            </div>
        </div>
    </div>
</div>
