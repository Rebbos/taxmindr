<?php
/**
 * TaxMindr - Home Page
 * Philippine Tax Compliance Platform
 */

require_once '../config/config.php';

$pageTitle = 'Home - TaxMindr';
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
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="features.php">Features</a></li>
                    <li><a href="about.php">About</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php" class="btn-primary">Get Started</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h2>Never Miss a Tax Deadline Again</h2>
                    <p>TaxMindr helps Filipinos stay compliant with BIR requirements through smart reminders, validation tools, and tax updates.</p>
                    <div class="hero-buttons">
                        <a href="register.php" class="btn-primary btn-large">Get Started Free</a>
                        <a href="features.php" class="btn-secondary btn-large">Learn More</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Problem Section -->
        <section class="problems">
            <div class="container">
                <h2>Common Tax Compliance Challenges</h2>
                <div class="problem-grid">
                    <div class="problem-card">
                        <div class="icon">⏰</div>
                        <h3>Missed Deadlines</h3>
                        <p>People miss tax deadlines and face penalties due to lack of reminders.</p>
                    </div>
                    <div class="problem-card">
                        <div class="icon">📋</div>
                        <h3>Complex Rules</h3>
                        <p>Tax rules change frequently and are hard to notice or understand.</p>
                    </div>
                    <div class="problem-card">
                        <div class="icon">❌</div>
                        <h3>Filing Errors</h3>
                        <p>Errors in withholding lists cause rework and compliance risks.</p>
                    </div>
                    <div class="problem-card">
                        <div class="icon">🔄</div>
                        <h3>Multiple Taxes</h3>
                        <p>Small businesses juggle Income Tax, VAT, and various withholding taxes.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Overview -->
        <section class="features-overview">
            <div class="container">
                <h2>How TaxMindr Helps</h2>
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon">📅</div>
                        <div class="feature-content">
                            <h3>Deadline Calendar & Reminders</h3>
                            <p>Auto-generated dates based on your profile. Get email and SMS reminders. Track filed and paid status.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">📰</div>
                        <div class="feature-content">
                            <h3>Tax Updates with Actions</h3>
                            <p>Short summaries of BIR updates, who's affected, what to do, and links to official sources.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">📊</div>
                        <div class="feature-content">
                            <h3>Withholding List Validation</h3>
                            <p>Upload Excel/CSV files. Automatically validate TINs, ATC codes, and totals. Download clean reports.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">✅</div>
                        <div class="feature-content">
                            <h3>Final Check Before Submit</h3>
                            <p>Critical error flagging. Required receipt/screenshot uploads. Ensure nothing is missed.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">📁</div>
                        <div class="feature-content">
                            <h3>Reports & Archive</h3>
                            <p>Generate exportable reports. Securely store past filings in a searchable archive.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta">
            <div class="container">
                <h2>Ready to Simplify Your Tax Compliance?</h2>
                <p>Join Filipino freelancers, businesses, and organizations staying on top of their tax responsibilities.</p>
                <a href="register.php" class="btn-primary btn-large">Start Using TaxMindr</a>
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
