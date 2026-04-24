<?php 
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db_connect.php'; 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$user_stmt = $pdo->query("SELECT id, username FROM users ORDER BY username ASC");
?>

<div class="main-content">
    <div class="mb-4">
        <a href="inventory.php" class="text-decoration-none text-secondary small"><i class="bi bi-arrow-left"></i> Back to Inventory</a>
        <h2 class="fw-bold mt-2 text-primary">Register New Asset</h2>
    </div>

    <div class="inventory-table-card p-5" style="max-width: 1000px;">
        <form action="process_asset.php" method="POST">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Property Code *</label>
                    <input type="text" name="property_code" class="form-control" placeholder="e.g. PROP-2026-001" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Serial Number * (unique)</label>
                    <input type="text" name="serial_number" class="form-control" placeholder="Manufacturer serial / barcode" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Item Name *</label>
                    <input type="text" name="item_name" class="form-control" placeholder="e.g. MacBook Pro M3" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php while($cat = $cat_stmt->fetch()): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Assigned Office / Department</label>
                    <input type="text" name="assigned_office" class="form-control" placeholder="e.g. IT Dept, Finance, Admin">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Officer In-Charge</label>
                    <input type="text" name="officer_in_charge" class="form-control" placeholder="Full name or position">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">System User (Assignee)</label>
                    <select name="responsible_user_id" class="form-select">
                        <option value="">-- Unassigned --</option>
                        <?php while($usr = $user_stmt->fetch()): ?>
                            <option value="<?= $usr['id'] ?>"><?= htmlspecialchars($usr['username']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <small class="text-muted">Optional: link to a system user account</small>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>