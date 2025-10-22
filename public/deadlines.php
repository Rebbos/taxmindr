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

// Include modern head
include '../components/head.php';
?>

<body>
    <!-- Decorative topbar accent line at the very top -->
    <?php include '../components/topbar.php'; ?>
    
    <!-- Include modern navbar -->
    <?php include '../components/navbar.php'; ?>
    
    <div class="d-flex">
        <!-- Include modern sidebar -->
        <?php include '../components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-wrapper flex-grow-1 d-flex flex-column">
            
            <?php
            // Set breadcrumbs and page actions
            $breadcrumbs = [
                ['label' => 'Deadlines', 'url' => '#']
            ];
            $pageActions = [
                ['label' => 'Add Deadline', 'url' => 'add_deadline.php', 'icon' => 'plus-lg', 'style' => 'primary']
            ];
            include '../components/breadcrumb.php';
            ?>
            
            <div class="flex-grow-1">
                <!-- Page Header -->
                <div class="mb-4 fade-in-up">
                    <h1 class="h3 fw-bold mb-2">
                        <i class="bi bi-calendar-event me-2"></i>Tax Deadlines
                    </h1>
                    <p class="text-muted mb-0">Track and manage your tax filing deadlines</p>
                </div>
            
            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Period:</label>
                            <select id="period-filter" class="form-select" onchange="applyFilters()">
                                <option value="upcoming" <?php echo $period === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                <option value="overdue" <?php echo $period === 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                                <option value="this_month" <?php echo $period === 'this_month' ? 'selected' : ''; ?>>This Month</option>
                                <option value="all" <?php echo $period === 'all' ? 'selected' : ''; ?>>All Time</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Status:</label>
                            <select id="status-filter" class="form-select" onchange="applyFilters()">
                                <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Status</option>
                                <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="filed" <?php echo $status === 'filed' ? 'selected' : ''; ?>>Filed</option>
                                <option value="paid" <?php echo $status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="late" <?php echo $status === 'late' ? 'selected' : ''; ?>>Late</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Deadlines List -->
            <div class="card">
                <?php if (empty($deadlines)): ?>
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">No deadlines found</h3>
                        <p class="text-muted">Add your first tax deadline to get started</p>
                        <a href="add_deadline.php" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-lg me-1"></i>Add Deadline
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tax Type</th>
                                    <th>BIR Form</th>
                                    <th>Period</th>
                                    <th>Deadline</th>
                                    <th>Days Left</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
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
                                            <?php
                                            $badgeMap = [
                                                'pending' => 'bg-warning text-dark',
                                                'filed' => 'bg-info',
                                                'paid' => 'bg-success',
                                                'late' => 'bg-danger'
                                            ];
                                            $badgeClass = $badgeMap[$deadline['status']] ?? 'bg-secondary';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo ucfirst($deadline['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <?php if ($deadline['status'] === 'pending'): ?>
                                                    <a href="mark_filed.php?id=<?php echo $deadline['deadline_id']; ?>" class="btn btn-outline-success" title="Mark Filed">
                                                        <i class="bi bi-check2"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="edit_deadline.php?id=<?php echo $deadline['deadline_id']; ?>" class="btn btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            </div>
            
            <!-- Include page footer -->
            <?php include '../components/page-footer.php'; ?>
        </main>
    </div>
    
    <script>
        function applyFilters() {
            const period = document.getElementById('period-filter').value;
            const status = document.getElementById('status-filter').value;
            window.location.href = `deadlines.php?period=${period}&status=${status}`;
        }
    </script>
    
    <!-- Include modern footer -->
    <?php include '../components/foot.php'; ?>
    
    <!-- Include modern footer -->
    <?php include '../components/foot.php'; ?>

<style>
    /* Deadline-specific styles */
        
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
        

