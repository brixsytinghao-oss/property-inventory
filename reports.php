<?php 
// 1. SESSION AND SECURITY GATE
session_start(); 

// Check if user is logged in before allowing access
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php'; 

// 2. FINANCIAL DATA LOGIC
// Calculate Total Valuation of all assets
$total_value = $pdo->query("SELECT SUM(value) FROM properties")->fetchColumn();

// Get Assets count and value per Category
$cat_sql = "SELECT c.category_name, COUNT(p.id) as asset_count, SUM(p.value) as total_cat_value 
            FROM categories c 
            LEFT JOIN properties p ON c.id = p.category_id 
            GROUP BY c.id";
$cat_stmt = $pdo->query($cat_sql);

// Get total asset count
$total_assets = $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();

// 3. PAGE SHELL
include 'includes/header.php'; 
include 'includes/sidebar.php'; 
?>

<!-- Print-specific styles -->
<style media="print">
    /* Hide sidebar and non-printable elements */
    .sidebar, .no-print, .btn, .btn-outline-primary, 
    .d-flex.justify-content-between .btn,
    .main-content .d-flex.justify-content-between .btn,
    .btn-action, .btn-group, .btn-link,
    .alert, .alert-dismissible {
        display: none !important;
    }
    
    /* Reset main content area for print */
    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
        padding: 0 !important;
    }
    
    /* Ensure full width and proper margins */
    body {
        background: white;
        margin: 0;
        padding: 0.5in;
        font-size: 12pt;
    }
    
    /* Keep card borders and shadows minimal */
    .inventory-table-card {
        box-shadow: none;
        border: 1px solid #ddd;
        margin-top: 10px;
    }
    
    /* Table styles for print */
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
    
    /* Keep headers visible */
    h1, h2, h3, h4, h5 {
        page-break-after: avoid;
    }
    
    /* Ensure page breaks don't cut rows */
    tr {
        page-break-inside: avoid;
    }
    
    /* Show print header */
    .print-header {
        display: block !important;
    }
</style>

<!-- Printable header (hidden on screen, visible when printing) -->
<div class="print-header" style="display: none; text-align: center; margin-bottom: 30px;">
    <h2>ASSETFLOW - Financial Report</h2>
    <p>Generated on: <?php echo date('F d, Y h:i A'); ?></p>
    <hr>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="fw-bold m-0">Financial Reports</h2>
            <p class="text-muted">Summary of inventory valuation and distribution.</p>
        </div>
        <button class="btn btn-primary shadow-sm" onclick="window.print();">
            <i class="bi bi-printer"></i> Print / Save as PDF
        </button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stat-card total p-4 bg-white shadow-sm border-start border-primary border-4 rounded-3">
                <small class="text-muted fw-bold text-uppercase">Total Inventory Valuation</small>
                <h1 class="fw-bold text-primary mt-2">
                    ₱<?php echo number_format($total_value, 2); ?>
                </h1>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card total p-4 bg-white shadow-sm border-start border-success border-4 rounded-3">
                <small class="text-muted fw-bold text-uppercase">Total Number of Assets</small>
                <h1 class="fw-bold text-success mt-2">
                    <?php echo number_format($total_assets); ?>
                </h1>
            </div>
        </div>
    </div>

    <div class="inventory-table-card shadow-sm border-0 rounded-3 overflow-hidden bg-white">
        <div class="p-4 border-bottom bg-light">
            <h5 class="fw-bold m-0 text-dark">Asset Distribution by Category</h5>
        </div>
        <table class="table table-hover align-middle mb-0">
            <thead class="text-muted small text-uppercase bg-white">
                <tr>
                    <th class="ps-4 py-3">Category Name</th>
                    <th class="py-3">Asset Count</th>
                    <th class="text-end pe-4 py-3">Estimated Value</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grand_total = 0;
                while($report = $cat_stmt->fetch()): 
                    $cat_value = $report['total_cat_value'] ?: 0;
                    $grand_total += $cat_value;
                ?>
                <tr>
                    <td class="ps-4 fw-bold text-dark">
                        <?php echo htmlspecialchars($report['category_name']); ?>
                    </td>
                    <td class="text-secondary">
                        <?php echo $report['asset_count'] ?: '0'; ?> Items
                    </td>
                    <td class="text-end pe-4 fw-bold text-success">
                        ₱<?php echo number_format($cat_value, 2); ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($cat_stmt->rowCount() == 0): ?>
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted">
                        No categories or assets found.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <td class="ps-4">TOTAL</td>
                    <td></td>
                    <td class="text-end pe-4">₱<?php echo number_format($grand_total, 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <!-- Footer note for print -->
    <div class="text-muted small text-center mt-4 no-print">
        <i class="bi bi-info-circle"></i> Click the Print button to generate a PDF or paper copy.
    </div>
</div>

<script>
    // Optional: Show/hide print header automatically (already handled by CSS media print)
    // No additional JS needed as CSS @media print handles everything.
    // For browsers that need a little help:
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