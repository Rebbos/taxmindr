<?php
/**
 * TaxMindr - Features Page
 * Philippine Tax Compliance Platform
 */

require_once '../config/config.php';

$pageTitle = 'Features - TaxMindr';
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
                    <li><a href="features.php" class="active">Features</a></li>
                    <li><a href="about.php">About</a></li>
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
                <h1>Features</h1>
                <p>Everything you need to stay tax-compliant in the Philippines</p>
            </div>
        </section>

        <!-- Detailed Features -->
        <section class="features-detailed">
            <div class="container">
                <!-- Deadline Management -->
                <div class="feature-block">
                    <div class="feature-icon-large">📅</div>
                    <h2>Deadline Calendar & Reminders</h2>
                    <p>Never miss another tax deadline with our intelligent reminder system.</p>
                    <ul>
                        <li>Auto-generated tax deadlines based on your profile (quarterly, monthly, annual)</li>
                        <li>Email and SMS reminders at customizable intervals</li>
                        <li>Track filed and paid status for each deadline</li>
                        <li>Visual calendar view of all upcoming obligations</li>
                        <li>Support for multiple tax types (Income Tax, VAT, Withholding, etc.)</li>
                    </ul>
                </div>

                <!-- Tax Updates -->
                <div class="feature-block">
                    <div class="feature-icon-large">📰</div>
                    <h2>Tax Updates with Actionable Insights</h2>
                    <p>Stay informed about BIR changes that affect you.</p>
                    <ul>
                        <li>Curated summaries of BIR revenue regulations and memorandums</li>
                        <li>Who's affected and what actions to take</li>
                        <li>Direct links to official BIR sources</li>
                        <li>Filter updates by relevance to your business type</li>
                        <li>Save and bookmark important updates for reference</li>
                    </ul>
                </div>

                <!-- Validation Tools -->
                <div class="feature-block">
                    <div class="feature-icon-large">📊</div>
                    <h2>Withholding List Validation</h2>
                    <p>Catch errors before you file and save time on rework.</p>
                    <ul>
                        <li>Upload Excel/CSV files for automatic validation</li>
                        <li>Verify TIN format and check digit accuracy</li>
                        <li>Validate ATC codes against BIR's official list</li>
                        <li>Auto-calculate and verify tax totals</li>
                        <li>Download cleaned reports ready for eBIRForms</li>
                        <li>Highlight errors with clear explanations</li>
                    </ul>
                </div>

                <!-- Submission Checklist -->
                <div class="feature-block">
                    <div class="feature-icon-large">✅</div>
                    <h2>Final Check Before Submit</h2>
                    <p>Ensure nothing is missed with our comprehensive pre-flight checklist.</p>
                    <ul>
                        <li>Critical error flagging for common mistakes</li>
                        <li>Required document uploads (receipts, screenshots)</li>
                        <li>Verification of payment details</li>
                        <li>Confirmation of all supporting documents</li>
                        <li>Step-by-step filing guide for each tax type</li>
                    </ul>
                </div>

                <!-- Reports & Archive -->
                <div class="feature-block">
                    <div class="feature-icon-large">📁</div>
                    <h2>Reports & Filing Archive</h2>
                    <p>Organized record-keeping for audits and reference.</p>
                    <ul>
                        <li>Generate exportable compliance reports (PDF, Excel)</li>
                        <li>Securely store all past filings in searchable archive</li>
                        <li>Track filing history by tax type and period</li>
                        <li>Quick access to payment receipts and confirmations</li>
                        <li>Multi-year compliance overview dashboard</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta">
            <div class="container">
                <h2>Ready to Get Started?</h2>
                <p>Join hundreds of Filipinos simplifying their tax compliance</p>
                <a href="../public/register.php" class="btn-primary btn-large">Create Free Account</a>
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
