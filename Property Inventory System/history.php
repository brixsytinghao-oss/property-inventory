<?php 
session_start();
include 'db_connect.php'; 

/**
 * 1. SECURITY GATE – Only admins can view this page
 */
$is_admin = false;
if (isset($_SESSION['role'])) {
    $current_role = strtolower($_SESSION['role']);
    if (strpos($current_role, 'admin') !== false) {
        $is_admin = true;
    }
}

if (!isset($_SESSION['user_id']) || !$is_admin) { 
    header("Location: index.php?error=unauthorized");
    exit();
}

include 'includes/header.php'; 
include 'includes/sidebar.php'; 

/**
 * 2. SEARCH LOGIC
 */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Base query
$query = "SELECT h.*, u.username FROM history_log h 
          JOIN users u ON h.user_id = u.id";

// Add search condition if search term is provided
if (!empty($search)) {
    $query .= " WHERE h.action_type LIKE :search 
                OR u.username LIKE :search 
                OR h.property_code LIKE :search 
                OR h.details LIKE :search";
}

$query .= " ORDER BY h.created_at DESC";

$stmt = $pdo->prepare($query);

if (!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bindParam(':search', $searchParam);
}

$stmt->execute();
?>

<!-- Print-specific styles (hide sidebar, buttons, etc. when printing) -->
<style media="print">
    /* Hide sidebar and non-printable elements */
    .sidebar, .no-print, .btn, .btn-outline-primary, 
    .d-flex.justify-content-between .btn,
    .main-content .d-flex.justify-content-between .btn,
    .btn-action, .btn-group, .btn-link,
    .alert, .alert-dismissible,
    .search-form {
        display: none !important;
    }
    
    /* Reset main content area for print */
    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
        padding: 0 !important;
    }
    
    body {
        background: white;
        margin: 0;
        padding: 0.5in;
        font-size: 12pt;
    }
    
    .inventory-table-card {
        box-shadow: none;
        border: 1px solid #ddd;
        margin-top: 10px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th, td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
    }
    
    thead {
        background-color: #f2f2f2;
    }
    
    h1, h2, h3, h4, h5 {
        page-break-after: avoid;
    }
    
    tr {
        page-break-inside: avoid;
    }
    
    .print-header {
        display: block !important;
        text-align: center;
        margin-bottom: 20px;
    }
</style>

<!-- Printable header (hidden on screen, visible when printing) -->
<div class="print-header" style="display: none; text-align: center; margin-bottom: 30px;">
    <h2>ASSETFLOW - System Activity History</h2>
    <p>Generated on: <?php echo date('F d, Y h:i A'); ?></p>
    <?php if (!empty($search)): ?>
        <p><strong>Search filter:</strong> "<?php echo htmlspecialchars($search); ?>"</p>
    <?php endif; ?>
    <hr>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="fw-bold m-0">System Activity History</h2>
            <p class="text-muted">Audit trail of all property additions, modifications, and deletions.</p>
        </div>
        <div class="d-flex gap-3">
            <!-- Search Form -->
            <form action="history.php" method="GET" class="search-form">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" 
                           placeholder="Search by user, action, property code..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Filter</button>
                    <?php if (!empty($search)): ?>
                        <a href="history.php" class="btn btn-outline-secondary px-3">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
            <!-- Print button -->
            <button class="btn btn-primary shadow-sm" onclick="window.print();">
                <i class="bi bi-printer"></i> Print / PDF
            </button>
        </div>
    </div>

    <!-- Search results info -->
    <?php if (!empty($search)): ?>
        <div class="alert alert-info alert-dismissible fade show mb-3 no-print" role="alert">
            <i class="bi bi-search"></i> Showing results for: <strong>"<?php echo htmlspecialchars($search); ?>"</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="inventory-table-card shadow-sm border-0 rounded-3 overflow-hidden">
        <table class="table table-hover align-middle mb-0 bg-white">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Timestamp</th>
                    <th class="py-3">User</th>
                    <th class="py-3">Action</th>
                    <th class="py-3">Property Code</th>
                    <th class="py-3">Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($stmt->rowCount() > 0): ?>
                    <?php while($log = $stmt->fetch()): ?>
                    <tr>
                        <td class="ps-4 text-muted small">
                            <?= date('M d, Y h:i A', strtotime($log['created_at'])) ?>
                        </td>
                        <td>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($log['username']) ?></span>
                        </td>
                        <td>
                            <?php 
                                $badge_class = 'bg-primary'; // Default (Update)
                                if ($log['action_type'] == 'Add') $badge_class = 'bg-success';
                                if ($log['action_type'] == 'Delete') $badge_class = 'bg-danger';
                                if ($log['action_type'] == 'Repair') $badge_class = 'bg-info';
                                if ($log['action_type'] == 'Permission') $badge_class = 'bg-warning';
                                if ($log['action_type'] == 'UserDelete') $badge_class = 'bg-dark';
                            ?>
                            <span class="badge rounded-pill <?= $badge_class ?> px-3 py-2">
                                <?= $log['action_type'] ?>
                            </span>
                        </td>
                        <td class="fw-bold text-primary">
                            <code><?= htmlspecialchars($log['property_code']) ?></code>
                        </td>
                        <td class="text-secondary small">
                            <?= htmlspecialchars($log['details']) ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                            <p class="text-muted mb-0">No activity logs found matching <strong>"<?php echo htmlspecialchars($search); ?>"</strong></p>
                            <a href="history.php" class="btn btn-link btn-sm mt-2">Clear search and view all</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Footer note for print (hidden on screen) -->
    <div class="text-muted small text-center mt-4 no-print">
        <i class="bi bi-info-circle"></i> Use the search box to filter logs. Click Print to generate a PDF.
    </div>
</div>

<script>
    // Show/hide print header automatically when printing
    window.onbeforeprint = function() {
        var printHeader = document.querySelector('.print-header');
        if (printHeader) printHeader.style.display = 'block';
    };
    window.onafterprint = function() {
        var printHeader = document.querySelector('.print-header');
        if (printHeader) printHeader.style.display = 'none';
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>