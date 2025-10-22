<!-- Header Navigation -->
<header class="dashboard-header-nav">
    <div class="header-content">
        <div class="logo">
            <a href="dashboard.php">
                <h1>TaxMindr</h1>
            </a>
        </div>
        
        <div class="header-actions">
            <div class="user-menu">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                <a href="settings.php" class="icon-btn" title="Settings">⚙️</a>
                <a href="logout.php" class="icon-btn" title="Logout">🚪</a>
            </div>
        </div>
    </div>
</header>
