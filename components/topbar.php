<!-- Top Bar (Breadcrumb & Page Actions) -->
<div class="topbar bg-white border-bottom py-3 mb-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?php echo APP_URL; ?>/public/dashboard.php">
                            <i class="bi bi-house-door"></i>
                        </a>
                    </li>
                    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                        <?php foreach ($breadcrumbs as $key => $crumb): ?>
                            <?php if ($key === array_key_last($breadcrumbs)): ?>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($crumb['label']); ?></li>
                            <?php else: ?>
                                <li class="breadcrumb-item">
                                    <a href="<?php echo htmlspecialchars($crumb['url']); ?>"><?php echo htmlspecialchars($crumb['label']); ?></a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </nav>
            
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
        </div>
    </div>
</div>
