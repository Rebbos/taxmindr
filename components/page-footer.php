<!-- Page Footer (for internal pages with sidebar) -->
<footer class="page-footer bg-white border-top mt-auto py-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 small text-muted">
                    &copy; <?php echo date('Y'); ?> <strong>TaxMindr</strong>. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="d-flex justify-content-center justify-content-md-end gap-3">
                    <a href="<?php echo BIR_WEBSITE; ?>" target="_blank" class="small text-muted text-decoration-none" title="BIR Official Website">
                        <i class="bi bi-box-arrow-up-right me-1"></i>BIR Website
                    </a>
                    <a href="<?php echo BIR_EBIRFORMS; ?>" target="_blank" class="small text-muted text-decoration-none" title="eBIRForms Portal">
                        <i class="bi bi-box-arrow-up-right me-1"></i>eBIRForms
                    </a>
                    <a href="#" class="small text-muted text-decoration-none">
                        <i class="bi bi-question-circle me-1"></i>Help
                    </a>
                    <a href="<?php echo APP_URL; ?>/public/settings.php" class="small text-muted text-decoration-none">
                        <i class="bi bi-gear me-1"></i>Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
.page-footer {
    margin-left: 0;
    transition: margin-left 0.3s ease;
}

/* Adjust footer when sidebar is open (desktop) */
@media (min-width: 992px) {
    .page-footer {
        margin-left: var(--sidebar-width);
    }
    
    .page-footer.sidebar-closed {
        margin-left: 80px;
    }
}
</style>

<script>
// Sync footer with sidebar state
document.addEventListener('DOMContentLoaded', function() {
    const footer = document.querySelector('.page-footer');
    const sidebar = document.getElementById('sidebar');
    
    if (footer && sidebar && window.innerWidth >= 992) {
        // Check initial state
        if (sidebar.classList.contains('closed')) {
            footer.classList.add('sidebar-closed');
        }
        
        // Listen for sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth >= 992) {
                    footer.classList.toggle('sidebar-closed');
                }
            });
        }
    }
});
</script>
