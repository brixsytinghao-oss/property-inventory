<?php 
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db_connect.php'; 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$id]);
$asset = $stmt->fetch();
if (!$asset) { header("Location: inventory.php"); exit(); }

$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$user_stmt = $pdo->query("SELECT id, username FROM users ORDER BY username ASC");
?>

<div class="main-content">
    <div class="mb-4">
        <a href="inventory.php" class="text-decoration-none text-secondary small"><i class="bi bi-arrow-left"></i> Back to Inventory</a>
        <h2 class="fw-bold mt-2 text-primary">Edit Asset Detail</h2>
    </div>

    <div class="inventory-table-card p-5" style="max-width: 1000px;">
        <form action="update_asset.php" method="POST">
            <input type="hidden" name="id" value="<?= $asset['id'] ?>">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Property Code</label>
                    <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($asset['property_code']) ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Serial Number</label>
                    <input type="text" name="serial_number" class="form-control" value="<?= htmlspecialchars($asset['serial_number']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Item Name</label>
                    <input type="text" name="item_name" class="form-control" value="<?= htmlspecialchars($asset['item_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Category</label>
                    <select name="category_id" class="form-select" required>
                        <?php while($cat = $cat_stmt->fetch()): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($asset['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Assigned Office</label>
                    <input type="text" name="assigned_office" class="form-control" value="<?= htmlspecialchars($asset['assigned_office'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Officer In-Charge</label>
                    <input type="text" name="officer_in_charge" class="form-control" value="<?= htmlspecialchars($asset['officer_in_charge'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">System User (Assignee)</label>
                    <select name="responsible_user_id" class="form-select">
                        <option value="">-- Unassigned --</option>
                        <?php while($usr = $user_stmt->fetch()): ?>
                            <option value="<?= $usr['id'] ?>" <?= ($asset['responsible_user_id'] == $usr['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($usr['username']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Asset Value (₱)</label>
                    <input type="number" name="value" class="form-control" value="<?= $asset['value'] ?>" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Purchase Date</label>
                    <input type="date" name="purchase_date" class="form-control" value="<?= $asset['purchase_date'] ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Status</label>
                    <select name="status" class="form-select">
                        <option value="Available" <?= $asset['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                        <option value="In Use" <?= $asset['status'] == 'In Use' ? 'selected' : '' ?>>In Use</option>
                        <option value="Maintenance" <?= $asset['status'] == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        <option value="Disposed" <?= $asset['status'] == 'Disposed' ? 'selected' : '' ?>>Disposed</option>
                    </select>
                </div>
            </div>
            <div class="mt-5 d-flex gap-2">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">UPDATE ASSET</button>
                <a href="inventory.php" class="btn btn-light px-4 py-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>