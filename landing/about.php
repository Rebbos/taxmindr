<?php
/**
 * TaxMindr - About Page
 * Philippine Tax Compliance Platform
 */

require_once '../config/config.php';

$pageTitle = 'About Us - TaxMindr';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <h1>TaxMindr</h1>
                    <p class="tagline">Stay on time, error-free</p>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="features.php">Features</a></li>
                    <li><a href="about.php" class="active">About</a></li>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li><a href="../admin/dashboard.php">Admin Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="../public/dashboard.php">Dashboard</a></li>
                        <?php endif; ?>
                        <li><a href="../public/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="../public/login.php">Login</a></li>
                        <li><a href="../public/register.php" class="btn-primary">Get Started</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <!-- Page Header -->
        <section class="page-header">
            <div class="container">
                <h1>About TaxMindr</h1>
                <p>Simplifying Philippine tax compliance for everyone</p>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="about-content">
            <div class="container">
                <div class="about-block">
                    <h2>Our Mission</h2>
                    <p>TaxMindr was created to solve a common problem in the Philippines: <strong>tax compliance is complicated, stressful, and often results in missed deadlines or filing errors.</strong></p>
                    
                    <p>We believe that staying compliant shouldn't require hiring expensive accountants or spending hours tracking BIR updates. Our platform makes tax compliance accessible, automated, and stress-free for:</p>
                    
                    <ul>
                        <li>Freelancers and self-employed professionals</li>
                        <li>Small and medium businesses</li>
                        <li>Startups navigating Philippine tax requirements</li>
                        <li>Anyone who wants to stay on top of their tax obligations</li>
                    </ul>
                </div>

                <div class="about-block">
                    <h2>The Problem We're Solving</h2>
                    <p>Many Filipinos struggle with:</p>
                    <ul>
                        <li><strong>Missed Deadlines:</strong> No centralized system to track when taxes are due</li>
                        <li><strong>Complex Regulations:</strong> BIR rules change frequently and are hard to understand</li>
                        <li><strong>Filing Errors:</strong> Manual validation of TINs and ATC codes leads to costly mistakes</li>
                        <li><strong>Scattered Information:</strong> Tax updates buried in lengthy memorandums</li>
                        <li><strong>Poor Record-Keeping:</strong> Past filings lost or disorganized</li>
                    </ul>
                </div>

                <div class="about-block">
                    <h2>Our Solution</h2>
                    <p>TaxMindr brings together everything you need in one platform:</p>
                    <ul>
                        <li>Automated deadline tracking and reminders</li>
                        <li>Curated, actionable tax updates from BIR</li>
                        <li>Validation tools to catch errors before filing</li>
                        <li>Pre-flight checklists to ensure nothing is missed</li>
                        <li>Secure archive for all your tax records</li>
                    </ul>
                </div>

                <div class="about-block">
                    <h2>Who We Are</h2>
                    <p>TaxMindr is developed by <strong>ASTIGORITHM</strong>, a team passionate about using technology to solve real-world problems in the Philippines.</p>
                    
                    <p>We understand the challenges of Philippine tax compliance firsthand and are committed to making it easier for everyone.</p>
                </div>

                <div class="about-block">
                    <h2>Our Commitment</h2>
                    <ul>
                        <li><strong>Accuracy:</strong> All tax information is sourced from official BIR publications</li>
                        <li><strong>Security:</strong> Your data is encrypted and stored securely</li>
                        <li><strong>Privacy:</strong> We never share your information without consent</li>
                        <li><strong>Support:</strong> We're here to help you succeed</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta">
            <div class="container">
                <h2>Join Us in Simplifying Tax Compliance</h2>
                <p>Start using TaxMindr today and never worry about missed deadlines again</p>
                <a href="../public/register.php" class="btn-primary btn-large">Get Started Free</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>TaxMindr</h4>
                    <p>Making Philippine tax compliance simple and stress-free.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="features.php">Features</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="<?php echo BIR_WEBSITE; ?>" target="_blank">BIR Website</a></li>
                        <li><a href="<?php echo BIR_EBIRFORMS; ?>" target="_blank">eBIRForms</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> TaxMindr by ASTIGORITHM. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>
</html>
