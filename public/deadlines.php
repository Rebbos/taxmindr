<?php
/**
 * Tax Deadlines Calendar
 * TaxMindr - Philippine Tax Compliance Platform
 */

require_once '../config/config.php';
requireLogin();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

// Get filter parameters
$status = $_GET['status'] ?? 'all';
$period = $_GET['period'] ?? 'upcoming';

// Build query
$query = "
    SELECT d.*, t.tax_name, t.bir_form, t.tax_code
    FROM tax_deadlines d
    JOIN tax_types t ON d.tax_type_id = t.tax_type_id
    WHERE d.user_id = ?
";

$params = [$userId];

if ($status !== 'all') {
    $query .= " AND d.status = ?";
    $params[] = $status;
}

if ($period === 'upcoming') {
    $query .= " AND d.deadline_date >= CURRENT_DATE()";
} elseif ($period === 'overdue') {
    $query .= " AND d.deadline_date < CURRENT_DATE() AND d.status = 'pending'";
} elseif ($period === 'this_month') {
    $query .= " AND MONTH(d.deadline_date) = MONTH(CURRENT_DATE()) AND YEAR(d.deadline_date) = YEAR(CURRENT_DATE())";
}

$query .= " ORDER BY d.deadline_date ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$deadlines = $stmt->fetchAll();

$pageTitle = 'Tax Deadlines - TaxMindr';
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
                    <h1>📅 Tax Deadlines</h1>
                    <p>Track and manage your tax filing deadlines</p>
                </div>
                <div>
                    <a href="add_deadline.php" class="btn-primary">+ Add Deadline</a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="filters-bar">
                <div class="filter-group">
                    <label>Period:</label>
                    <select id="period-filter" onchange="applyFilters()">
                        <option value="upcoming" <?php echo $period === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                        <option value="overdue" <?php echo $period === 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                        <option value="this_month" <?php echo $period === 'this_month' ? 'selected' : ''; ?>>This Month</option>
                        <option value="all" <?php echo $period === 'all' ? 'selected' : ''; ?>>All Time</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Status:</label>
                    <select id="status-filter" onchange="applyFilters()">
                        <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="filed" <?php echo $status === 'filed' ? 'selected' : ''; ?>>Filed</option>
                        <option value="paid" <?php echo $status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="late" <?php echo $status === 'late' ? 'selected' : ''; ?>>Late</option>
                    </select>
                </div>
            </div>
            
            <!-- Deadlines List -->
            <div class="content-card">
                <?php if (empty($deadlines)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📅</div>
                        <h3>No deadlines found</h3>
                        <p>Add your first tax deadline to get started</p>
                        <a href="add_deadline.php" class="btn-primary">Add Deadline</a>
                    </div>
                <?php else: ?>
                    <div class="deadlines-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tax Type</th>
                                    <th>BIR Form</th>
                                    <th>Period</th>
                                    <th>Deadline</th>
                                    <th>Days Left</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deadlines as $deadline): ?>
                                    <?php
                                    $daysLeft = daysUntilDeadline($deadline['deadline_date']);
                                    $urgencyClass = $daysLeft < 0 ? 'overdue' : ($daysLeft <= 3 ? 'urgent' : ($daysLeft <= 7 ? 'warning' : 'normal'));
                                    ?>
                                    <tr class="deadline-row <?php echo $urgencyClass; ?>">
                                        <td>
                                            <strong><?php echo htmlspecialchars($deadline['tax_name']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($deadline['bir_form']); ?></td>
                                        <td><?php echo htmlspecialchars($deadline['filing_period']); ?></td>
                                        <td><?php echo formatDate($deadline['deadline_date'], 'M d, Y'); ?></td>
                                        <td>
                                            <span class="days-badge <?php echo $urgencyClass; ?>">
                                                <?php 
                                                if ($daysLeft < 0) {
                                                    echo abs($daysLeft) . ' days overdue';
                                                } elseif ($daysLeft == 0) {
                                                    echo 'DUE TODAY';
                                                } else {
                                                    echo $daysLeft . ' day' . ($daysLeft != 1 ? 's' : '');
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $deadline['status']; ?>">
                                                <?php echo ucfirst($deadline['status']); ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <?php if ($deadline['status'] === 'pending'): ?>
                                                <a href="mark_filed.php?id=<?php echo $deadline['deadline_id']; ?>" class="btn-sm">Mark Filed</a>
                                            <?php endif; ?>
                                            <a href="edit_deadline.php?id=<?php echo $deadline['deadline_id']; ?>" class="btn-sm btn-secondary">Edit</a>
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
    <script>
        function applyFilters() {
            const period = document.getElementById('period-filter').value;
            const status = document.getElementById('status-filter').value;
            window.location.href = `deadlines.php?period=${period}&status=${status}`;
        }
    </script>
    
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 2rem;
        }
        
        .filters-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--bg-white);
            border-radius: 8px;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .filter-group label {
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .filter-group select {
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
        }
        
        .content-card {
            background: var(--bg-white);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        
        .deadlines-table {
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
            color: var(--text-primary);
            border-bottom: 2px solid var(--border-color);
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        .days-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .days-badge.normal {
            background: #d1fae5;
            color: #065f46;
        }
        
        .days-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .days-badge.urgent,
        .days-badge.overdue {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-filed {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-late {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</body>
</html>
