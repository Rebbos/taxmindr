<?php
/**
 * Tax Updates & BIR Announcements
 * TaxMindr - Philippine Tax Compliance Platform
 */

require_once '../config/config.php';
requireLogin();

$pdo = getDBConnection();
$user = $_SESSION;

// Get filter
$filter = $_GET['filter'] ?? 'all';

// Build query
$query = "
    SELECT * FROM tax_updates 
    WHERE is_active = 1
";

$params = [];

if ($filter === 'relevant') {
    $query .= " AND (affected_taxpayers LIKE ? OR affected_taxpayers LIKE '%all%')";
    $params[] = "%{$_SESSION['user_type']}%";
}

if ($filter === 'action_required') {
    $query .= " AND action_required = 1";
}

$query .= " ORDER BY published_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$updates = $stmt->fetchAll();

$pageTitle = 'Tax Updates - TaxMindr';

// Include modern head
include '../components/head.php';
?>

<body>
    <!-- Decorative topbar accent line at the very top -->
    <?php include '../components/topbar.php'; ?>
    
    <div class="d-flex">
        <!-- Include modern sidebar -->
        <?php include '../components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-wrapper flex-grow-1 d-flex flex-column">
            
            <?php
            $breadcrumbs = [
                ['label' => 'Tax Updates', 'url' => '#']
            ];
            include '../components/page-header.php';
            ?>
            
            <div class="flex-grow-1">
            <div class="page-header">
                <div>
                    <h1>📰 Tax Updates & BIR Announcements</h1>
                    <p>Stay informed about the latest tax rules and regulations</p>
                </div>
            </div>
            
            <!-- Filter Tabs -->
            <div class="tabs-bar">
                <a href="?filter=all" class="tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                    All Updates
                </a>
                <a href="?filter=relevant" class="tab <?php echo $filter === 'relevant' ? 'active' : ''; ?>">
                    Relevant to Me
                </a>
                <a href="?filter=action_required" class="tab <?php echo $filter === 'action_required' ? 'active' : ''; ?>">
                    ⚠️ Action Required
                </a>
            </div>
            
            <!-- Updates List -->
            <div class="content-card">
                <?php if (empty($updates)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📰</div>
                        <h3>No updates found</h3>
                        <p>Check back later for new BIR announcements</p>
                    </div>
                <?php else: ?>
                    <div class="updates-feed">
                        <?php foreach ($updates as $update): ?>
                            <article class="update-card">
                                <div class="update-header">
                                    <div>
                                        <h2><?php echo htmlspecialchars($update['title']); ?></h2>
                                        <div class="update-meta">
                                            <span class="date">📅 <?php echo formatDate($update['published_at'], 'M d, Y'); ?></span>
                                            <?php if ($update['effective_date']): ?>
                                                <span class="effective">Effective: <?php echo formatDate($update['effective_date'], 'M d, Y'); ?></span>
                                            <?php endif; ?>
                                            <?php if ($update['bir_memo_number']): ?>
                                                <span class="memo">📄 <?php echo htmlspecialchars($update['bir_memo_number']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if ($update['action_required']): ?>
                                        <span class="badge badge-warning">⚠️ Action Required</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="update-summary">
                                    <p><?php echo nl2br(htmlspecialchars($update['summary'])); ?></p>
                                </div>
                                
                                <div class="update-affected">
                                    <strong>Who is affected:</strong>
                                    <div class="affected-tags">
                                        <?php 
                                        $affected = explode(',', $update['affected_taxpayers']);
                                        foreach ($affected as $type): 
                                            $cleanType = trim($type);
                                        ?>
                                            <span class="tag"><?php echo ucfirst(htmlspecialchars($cleanType)); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <?php if ($update['action_items']): ?>
                                    <div class="update-actions-list">
                                        <strong>What you need to do:</strong>
                                        <div class="action-items">
                                            <?php echo nl2br(htmlspecialchars($update['action_items'])); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="update-footer">
                                    <?php if ($update['official_source_url']): ?>
                                        <a href="<?php echo htmlspecialchars($update['official_source_url']); ?>" 
                                           target="_blank" class="btn-secondary btn-sm">
                                            🔗 View Official Source
                                        </a>
                                    <?php endif; ?>
                                    <a href="update_details.php?id=<?php echo $update['update_id']; ?>" class="btn-primary btn-sm">
                                        Read Full Details →
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/main.js"></script>
    
    <style>
        .tabs-bar {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .tab {
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            color: var(--text-secondary);
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.3s;
        }
        
        .tab:hover,
        .tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .updates-feed {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .update-card {
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-white);
        }
        
        .update-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        
        .update-card h2 {
            font-size: 1.25rem;
            color: var(--text-primary);
            margin: 0 0 0.5rem 0;
        }
        
        .update-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        .update-summary {
            margin: 1rem 0;
            padding: 1rem;
            background: var(--bg-light);
            border-left: 3px solid var(--primary-color);
            border-radius: 4px;
        }
        
        .update-affected {
            margin: 1rem 0;
        }
        
        .affected-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .tag {
            padding: 0.25rem 0.75rem;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .update-actions-list {
            margin: 1rem 0;
            padding: 1rem;
            background: #fef3c7;
            border-radius: 8px;
        }
        
        .action-items {
            margin-top: 0.5rem;
            white-space: pre-line;
        }
        
        .update-footer {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
    </style>
    </div>
    
    <!-- Include page footer -->
    <?php include '../components/page-footer.php'; ?>
</main>
</div>

<!-- Include modern footer -->
<?php include '../components/foot.php'; ?>
