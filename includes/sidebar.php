<!-- Sidebar Navigation -->
<aside class="sidebar">
    <nav>
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="deadlines.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'deadlines.php' ? 'active' : ''; ?>">
                    <span class="icon">📅</span>
                    <span>Tax Deadlines</span>
                </a>
            </li>
            <li>
                <a href="upload_withholding.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'upload_withholding.php' ? 'active' : ''; ?>">
                    <span class="icon">📊</span>
                    <span>Withholding Lists</span>
                </a>
            </li>
            <li>
                <a href="updates.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'updates.php' ? 'active' : ''; ?>">
                    <span class="icon">📰</span>
                    <span>Tax Updates</span>
                </a>
            </li>
            <li>
                <a href="submissions.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'submissions.php' ? 'active' : ''; ?>">
                    <span class="icon">📁</span>
                    <span>Filing Archive</span>
                </a>
            </li>
            <li>
                <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>">
                    <span class="icon">📈</span>
                    <span>Reports</span>
                </a>
            </li>
            <li>
                <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                    <span class="icon">⚙️</span>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="help.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'help.php' ? 'active' : ''; ?>">
                    <span class="icon">❓</span>
                    <span>Help & Support</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <span class="icon">🚪</span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<style>
.sidebar-footer {
    position: absolute;
    bottom: 1rem;
    left: 0;
    right: 0;
    padding: 0 1rem;
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: var(--danger-color);
    text-decoration: none;
    transition: all 0.3s;
    border-radius: 8px;
}

.logout-btn:hover {
    background: #fee2e2;
}
</style>
