<?php 
// 1. SESSION AND SECURITY GATE
// session_start must be line 1 to ensure the sidebar knows your role
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

// 3. PAGE SHELL
include 'includes/header.php'; 
include 'includes/sidebar.php'; 
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0">Financial Reports</h2>
            <p class="text-muted">Summary of inventory valuation and distribution.</p>
        </div>
        <button class="btn btn-outline-primary shadow-sm" onclick="window.print()">
            <i class="bi bi-printer"></i> Print PDF
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
                <?php while($report = $cat_stmt->fetch()): ?>
                <tr>
                    <td class="ps-4 fw-bold text-dark">
                        <?php echo htmlspecialchars($report['category_name']); ?>
                    </td>
                    <td class="text-secondary">
                        <?php echo $report['asset_count'] ?: '0'; ?> Items
                    </td>
                    <td class="text-end pe-4 fw-bold text-success">
                        ₱<?php echo number_format($report['total_cat_value'], 2) ?: '0.00'; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>