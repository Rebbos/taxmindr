<!-- Modern Footer -->
<footer class="footer bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <!-- About Section -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="brand-icon me-2">
                        <i class="bi bi-calendar-check-fill text-primary fs-3"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Tax<span class="text-primary">Mindr</span></h5>
                </div>
                <p class="text-light-emphasis mb-3">
                    Making Philippine tax compliance simple, stress-free, and accessible for everyone.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-semibold mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?php echo APP_URL; ?>/public/index.php" class="text-light-emphasis text-decoration-none">
                            <i class="bi bi-chevron-right small me-1"></i>Home
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo APP_URL; ?>/public/features.php" class="text-light-emphasis text-decoration-none">
                            <i class="bi bi-chevron-right small me-1"></i>Features
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo APP_URL; ?>/public/about.php" class="text-light-emphasis text-decoration-none">
                            <i class="bi bi-chevron-right small me-1"></i>About Us
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo APP_URL; ?>/public/contact.php" class="text-light-emphasis text-decoration-none">
                            <i class="bi bi-chevron-right small me-1"></i>Contact
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Resources -->
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-semibold mb-3">Resources</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?php echo BIR_WEBSITE; ?>" target="_blank" class="text-light-emphasis text-decoration-none">
                            <i class="bi bi-box-arrow-up-right small me-1"></i>BIR Website
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo BIR_EBIRFORMS; ?>" target="_blank" class="text-light-emphasis text-decoration-none">
                            <i class="bi bi-box-arrow-up-right small me-1"></i>eBIRForms Portal
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-light-emphasis text-decoration-none">
                            <i class="bi bi-file-text small me-1"></i>Tax Calculator
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-light-emphasis text-decoration-none">
                            <i class="bi bi-book small me-1"></i>Documentation
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-semibold mb-3">Contact Us</h6>
                <ul class="list-unstyled">
                    <li class="mb-2 text-light-emphasis">
                        <i class="bi bi-envelope me-2"></i>
                        support@taxmindr.com
                    </li>
                    <li class="mb-2 text-light-emphasis">
                        <i class="bi bi-telephone me-2"></i>
                        +63 xxx xxx xxxx
                    </li>
                    <li class="mb-2 text-light-emphasis">
                        <i class="bi bi-geo-alt me-2"></i>
                        Metro Manila, Philippines
                    </li>
                </ul>
            </div>
        </div>
        
        <hr class="my-4 border-secondary">
        
        <!-- Copyright -->
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-light-emphasis small">
                    &copy; <?php echo date('Y'); ?> TaxMindr by <strong>ASTIGORITHM</strong>. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="#" class="text-light-emphasis text-decoration-none small me-3">Privacy Policy</a>
                <a href="#" class="text-light-emphasis text-decoration-none small me-3">Terms of Service</a>
                <a href="#" class="text-light-emphasis text-decoration-none small">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
