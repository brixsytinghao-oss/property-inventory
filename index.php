<?php 
// 1. SESSION AND SECURITY GATE
// session_start must be the absolute first line to ensure role data is loaded
session_start(); 

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. DATABASE AND DASHBOARD LOGIC
include 'db_connect.php'; 

// Fetch Dashboard Statistics for Metric Cards
$total_assets = $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();
$available    = $pdo->query("SELECT COUNT(*) FROM properties WHERE status='Available'")->fetchColumn();
$maintenance  = $pdo->query("SELECT COUNT(*) FROM properties WHERE status='Maintenance' OR status='In Repair'")->fetchColumn();

// 3. PAGE SHELL
include 'includes/header.php'; 
include 'includes/sidebar.php'; 
?>

<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold m-0">System Dashboard</h2>
            <p class="text-muted">Real-time overview of company assets and valuations.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-action"><i class="bi bi-printer"></i> Print Report</button>
            <a href="inventory.php" class="btn btn-primary btn-action px-4">Manage All Assets</a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card total border-start border-primary border-4 shadow-sm p-4 bg-white rounded-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted fw-bold">TOTAL ASSETS</small>
                        <h2 class="fw-bold mt-1"><?php echo $total_assets; ?></h2>
                    </div>
                    <i class="bi bi-layers text-primary fs-3"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-card available border-start border-success border-4 shadow-sm p-4 bg-white rounded-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted fw-bold">AVAILABLE</small>
                        <h2 class="fw-bold text-success mt-1"><?php echo $available; ?></h2>
                    </div>
                    <i class="bi bi-check-circle text-success fs-3"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card maintenance border-start border-warning border-4 shadow-sm p-4 bg-white rounded-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted fw-bold">IN REPAIR</small>
                        <h2 class="fw-bold text-warning mt-1"><?php echo $maintenance; ?></h2>
                    </div>
                    <i class="bi bi-tools text-warning fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="inventory-table-card shadow-sm border-0 rounded-3 overflow-hidden bg-white">
        <div class="p-4 border-bottom">
            <h5 class="fw-bold m-0">Recent Acquisitions</h5>
        </div>
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Code</th>
                    <th class="py-3">Item Name</th>
                    <th class="py-3">Date Added</th>
                    <th class="text-end pe-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch only the 5 most recently added items
                $stmt = $pdo->query("SELECT * FROM properties ORDER BY id DESC LIMIT 5");
                while($row = $stmt->fetch()) {
                ?>
                <tr>
                    <td class="ps-4 fw-bold text-primary">
                        <code><?php echo htmlspecialchars($row['property_code']); ?></code>
                    </td>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td class="text-muted">
                        <?php echo $row['purchase_date'] ? date('M d, Y', strtotime($row['purchase_date'])) : 'N/A'; ?>
                    </td>
                    <td class="text-end pe-4">
                        <?php 
                            $status_class = ($row['status'] == 'Available') ? 'bg-success' : 'bg-primary';
                            if ($row['status'] == 'Maintenance' || $row['status'] == 'In Repair') $status_class = 'bg-warning';
                        ?>
                        <span class="badge rounded-pill <?php echo $status_class; ?> px-3 py-2">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>