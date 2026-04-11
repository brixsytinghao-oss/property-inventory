<?php 
// 1. Session Security & Headers
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php'; 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// 2. Advanced Search Logic
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Joining tables to get the Category Name and sorting by newest first
$query = "SELECT p.*, c.category_name 
          FROM properties p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.item_name LIKE :search OR p.property_code LIKE :search
          ORDER BY p.id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute(['search' => "%$search%"]);
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-dark">Asset Inventory</h2>
            <p class="text-muted small">Manage and track all registered company properties.</p>
        </div>
        
        <div class="d-flex gap-3 align-items-center" style="max-width: 500px; width: 100%;">
            <form action="inventory.php" method="GET" class="flex-grow-1">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" 
                           placeholder="Search code or name..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Search</button>
                </div>
            </form>

            <?php if ($_SESSION['role'] === 'Admin'): ?>
                <a href="add_asset.php" class="btn btn-primary px-4 fw-bold shadow-sm text-nowrap">
                    <i class="bi bi-plus-lg"></i> Add Asset
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="inventory-table-card shadow-sm border-0 rounded-3 overflow-hidden">
        <table class="table table-hover align-middle mb-0 bg-white">
            <thead class="bg-light">
                <tr class="text-muted small text-uppercase">
                    <th class="ps-4 py-3">Code</th>
                    <th class="py-3">Item Details</th>
                    <th class="py-3">Category</th>
                    <th class="py-3 text-center">Status</th>
                    <th class="text-end pe-4 py-3">Management</th>
                </tr>
            </thead>
            <tbody>
                <?php if($stmt->rowCount() > 0): ?>
                    <?php while($row = $stmt->fetch()): ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-primary"><?php echo $row['property_code']; ?></span>
                        </td>
                        
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['item_name']); ?></div>
                            <small class="text-muted">Value: ₱<?php echo number_format($row['value'], 2); ?></small>
                        </td>
                        
                        <td>
                            <span class="badge bg-light text-dark border">
                                <?php echo $row['category_name'] ?? 'Unassigned'; ?>
                            </span>
                        </td>
                        
                        <td class="text-center">
                            <?php 
                                $status = $row['status'];
                                $badgeClass = 'bg-secondary'; 
                                if($status == 'Available') $badgeClass = 'bg-success-light text-success';
                                elseif($status == 'In Use') $badgeClass = 'bg-primary-light text-primary';
                                elseif($status == 'Maintenance') $badgeClass = 'bg-warning-light text-warning';
                                elseif($status == 'Disposed') $badgeClass = 'bg-secondary-light text-secondary';
                            ?>
                            <span class="badge rounded-pill <?php echo $badgeClass; ?> px-3 py-2" style="min-width: 95px;">
                                <?php echo $status; ?>
                            </span>
                        </td>
                        
                        <td class="text-end pe-4">
                            <?php if ($_SESSION['role'] === 'Admin'): ?>
                                <div class="btn-group gap-2">
                                    <a href="edit_asset.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-secondary border-0 rounded-circle p-2" 
                                       title="Edit Asset">
                                        <i class="bi bi-pencil fs-5"></i>
                                    </a>
                                    
                                    <a href="delete_asset.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger border-0 rounded-circle p-2" 
                                       title="Delete Asset"
                                       onclick="return confirm('Are you sure you want to permanently delete this asset?');">
                                        <i class="bi bi-trash fs-5"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-light text-muted fw-normal border">Read-Only</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                            <p class="text-muted mb-0">No assets found matching <strong>"<?php echo htmlspecialchars($search); ?>"</strong></p>
                            <a href="inventory.php" class="btn btn-link btn-sm mt-2">Clear search and view all</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>