<?php
/**
 * Filing Archive & Submissions
 * TaxMindr - Philippine Tax Compliance Platform
 */

require_once '../config/config.php';
requireLogin();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

// Get submissions
$stmt = $pdo->prepare("
    SELECT s.*, t.tax_name, t.bir_form, d.deadline_date
    FROM filing_submissions s
    JOIN tax_types t ON s.tax_type_id = t.tax_type_id
    LEFT JOIN tax_deadlines d ON s.deadline_id = d.deadline_id
    WHERE s.user_id = ?
    ORDER BY s.submission_date DESC
");
$stmt->execute([$userId]);
$submissions = $stmt->fetchAll();

$pageTitle = 'Filing Archive - TaxMindr';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="dashboard-main">
            <div class="page-header">
                <div>
                    <h1>📁 Filing Archive</h1>
                    <p>View and manage your past tax submissions</p>
                </div>
            </div>
            
            <div class="content-card">
                <?php if (empty($submissions)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📁</div>
                        <h3>No submissions yet</h3>
                        <p>Your filed tax returns will appear here</p>
                    </div>
                <?php else: ?>
                    <div class="submissions-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tax Type</th>
                                    <th>BIR Form</th>
                                    <th>Filing Period</th>
                                    <th>Submission Date</th>
                                    <th>Amount Paid</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($submissions as $submission): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($submission['tax_name']); ?></td>
                                        <td><?php echo htmlspecialchars($submission['bir_form']); ?></td>
                                        <td><?php echo htmlspecialchars($submission['filing_period']); ?></td>
                                        <td><?php echo formatDate($submission['submission_date'], 'M d, Y g:i A'); ?></td>
                                        <td><?php echo $submission['amount_paid'] ? formatCurrency($submission['amount_paid']) : '-'; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $submission['status']; ?>">
                                                <?php echo ucfirst($submission['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="submission_details.php?id=<?php echo $submission['submission_id']; ?>" class="btn-sm">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/main.js"></script>
    
    <style>
        .submissions-table {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: var(--bg-light);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .status-submitted {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</body>
</html>
