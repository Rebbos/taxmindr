<?php
/**
 * Upload Withholding List
 * TaxMindr - Philippine Tax Compliance Platform
 */

require_once '../config/config.php';
requireLogin();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['withholding_file'])) {
    $file = $_FILES['withholding_file'];
    $filingPeriod = sanitize($_POST['filing_period'] ?? '');
    $taxTypeId = (int)($_POST['tax_type_id'] ?? 0);
    
    // Validate file
    $validation = validateFileUpload($file, ['csv', 'xlsx', 'xls']);
    
    if (!$validation['success']) {
        $errors[] = $validation['message'];
    } elseif (empty($filingPeriod)) {
        $errors[] = 'Filing period is required';
    } elseif ($taxTypeId === 0) {
        $errors[] = 'Tax type is required';
    } else {
        // Save file
        $fileName = uniqid() . '_' . basename($file['name']);
        $uploadPath = UPLOAD_PATH . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            try {
                // Save to database
                $stmt = $pdo->prepare("
                    INSERT INTO withholding_uploads 
                    (user_id, file_name, file_path, file_type, filing_period, tax_type_id, validation_status)
                    VALUES (?, ?, ?, ?, ?, ?, 'pending')
                ");
                
                $stmt->execute([
                    $userId,
                    basename($file['name']),
                    $fileName,
                    $validation['extension'],
                    $filingPeriod,
                    $taxTypeId
                ]);
                
                $uploadId = $pdo->lastInsertId();
                
                // Log activity
                logActivity($pdo, $userId, 'file_upload', 'Withholding list uploaded: ' . $filingPeriod, 'withholding_uploads', $uploadId);
                
                $success = 'File uploaded successfully! Processing...';
                
                // Redirect to validation page
                header("Refresh: 2; url=validate_withholding.php?id=$uploadId");
            } catch (PDOException $e) {
                error_log("Upload Error: " . $e->getMessage());
                $errors[] = 'Failed to save file information';
            }
        } else {
            $errors[] = 'Failed to upload file';
        }
    }
}

// Get tax types for withholding
$stmt = $pdo->prepare("
    SELECT tax_type_id, tax_name, bir_form 
    FROM tax_types 
    WHERE tax_code LIKE '%WITHHOLDING%' OR tax_code LIKE '%EWT%' OR tax_code LIKE '%CWT%'
    ORDER BY tax_name
");
$stmt->execute();
$taxTypes = $stmt->fetchAll();

// Get recent uploads
$stmt = $pdo->prepare("
    SELECT w.*, t.tax_name 
    FROM withholding_uploads w
    LEFT JOIN tax_types t ON w.tax_type_id = t.tax_type_id
    WHERE w.user_id = ?
    ORDER BY w.upload_date DESC
    LIMIT 10
");
$stmt->execute([$userId]);
$recentUploads = $stmt->fetchAll();

$pageTitle = 'Upload Withholding List - TaxMindr';

// Include modern head
include '../components/head.php';
?>

<body>
    <!-- Include modern navbar -->
    <?php include '../components/navbar.php'; ?>
    
    <div class="d-flex">
        <!-- Include modern sidebar -->
        <?php include '../components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-wrapper flex-grow-1">
            <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
                <div>
                    <h1 class="h3 fw-bold mb-2">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Upload Withholding List</h1>
                    <p>Upload and validate your withholding tax lists</p>
                </div>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <p><?php echo $success; ?></p>
                </div>
            <?php endif; ?>
            
            <div class="content-grid">
                <!-- Upload Form -->
                <div class="content-card">
                    <h2>Upload New File</h2>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="tax_type_id">Tax Type *</label>
                            <select id="tax_type_id" name="tax_type_id" required>
                                <option value="">Select tax type</option>
                                <?php foreach ($taxTypes as $type): ?>
                                    <option value="<?php echo $type['tax_type_id']; ?>">
                                        <?php echo htmlspecialchars($type['tax_name']); ?> (<?php echo htmlspecialchars($type['bir_form']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="filing_period">Filing Period *</label>
                            <input type="text" id="filing_period" name="filing_period" 
                                   placeholder="e.g., January 2025" required>
                            <small>Format: Month Year (e.g., January 2025)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="withholding_file">Upload File *</label>
                            <input type="file" id="withholding_file" name="withholding_file" 
                                   accept=".csv,.xlsx,.xls" required>
                            <small>Accepted formats: CSV, Excel (.xlsx, .xls) - Max 10MB</small>
                        </div>
                        
                        <div class="form-info">
                            <strong>📋 Required Columns:</strong>
                            <ul>
                                <li>Payee TIN</li>
                                <li>Payee Name</li>
                                <li>ATC Code</li>
                                <li>Tax Base Amount</li>
                                <li>Tax Rate</li>
                                <li>Tax Withheld Amount</li>
                            </ul>
                            <a href="../assets/templates/withholding_template.xlsx" download class="btn-secondary btn-sm">
                                Download Template
                            </a>
                        </div>
                        
                        <button type="submit" class="btn-primary">Upload & Validate</button>
                    </form>
                </div>
                
                <!-- Recent Uploads -->
                <div class="content-card">
                    <h2>Recent Uploads</h2>
                    
                    <?php if (empty($recentUploads)): ?>
                        <p class="empty-state">No uploads yet</p>
                    <?php else: ?>
                        <div class="uploads-list">
                            <?php foreach ($recentUploads as $upload): ?>
                                <div class="upload-item">
                                    <div class="upload-icon">
                                        <?php echo $upload['file_type'] === 'csv' ? '📄' : '📊'; ?>
                                    </div>
                                    <div class="upload-info">
                                        <h4><?php echo htmlspecialchars($upload['file_name']); ?></h4>
                                        <p><?php echo htmlspecialchars($upload['tax_name']); ?> - <?php echo htmlspecialchars($upload['filing_period']); ?></p>
                                        <small><?php echo formatDate($upload['upload_date'], 'M d, Y g:i A'); ?></small>
                                    </div>
                                    <div class="upload-status">
                                        <span class="status-badge status-<?php echo $upload['validation_status']; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $upload['validation_status'])); ?>
                                        </span>
                                        <a href="validate_withholding.php?id=<?php echo $upload['upload_id']; ?>" class="btn-sm">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/main.js"></script>
    
    <style>
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        .form-info {
            background: var(--bg-light);
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        .form-info ul {
            margin: 0.5rem 0;
            padding-left: 1.5rem;
        }
        
        .form-info li {
            margin: 0.25rem 0;
        }
        
        .uploads-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .upload-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-light);
            border-radius: 8px;
        }
        
        .upload-icon {
            font-size: 2rem;
        }
        
        .upload-info {
            flex: 1;
        }
        
        .upload-info h4 {
            margin: 0 0 0.25rem 0;
            font-size: 0.95rem;
        }
        
        .upload-info p {
            margin: 0.25rem 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .upload-info small {
            color: var(--text-secondary);
            font-size: 0.8rem;
        }
        
        .upload-status {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-end;
        }
        
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    <!-- Include modern footer -->
    <?php include '../components/foot.php'; ?>
