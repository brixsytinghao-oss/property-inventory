<?php 
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db_connect.php'; 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Fetch categories for the dropdown
$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
?>

<div class="main-content">
    <div class="mb-4">
        <a href="inventory.php" class="text-decoration-none text-secondary small"><i class="bi bi-arrow-left"></i> Back to Inventory</a>
        <h2 class="fw-bold mt-2 text-primary">Register New Asset</h2>
    </div>

    <div class="inventory-table-card p-5" style="max-width: 900px;">
        <form action="process_asset.php" method="POST">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Property Code</label>
                    <input type="text" name="property_code" class="form-control" placeholder="e.g. PROP-2026-001" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Item Name</label>
                    <input type="text" name="item_name" class="form-control" placeholder="e.g. MacBook Pro" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php while($cat = $cat_stmt->fetch()): ?>
                            <option value="<?= $cat['id'] ?>"><?= $cat['category_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Asset Value (₱)</label>
                    <input type="number" name="value" class="form-control" step="0.01" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Purchase Date</label>
                    <input type="date" name="purchase_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Initial Status</label>
                    <select name="status" class="form-select">
                        <option value="Available">Available</option>
                        <option value="In Use">In Use</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
            </div>

            <div class="mt-5 d-flex gap-2">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">SAVE PROPERTY</button>
                <a href="inventory.php" class="btn btn-light px-4 py-2">Cancel</a>
            </div>
        </form>
    </div>
</div>