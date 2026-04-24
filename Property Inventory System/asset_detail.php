<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db_connect.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$asset_id = $_GET['id'] ?? 0;
$asset_stmt = $pdo->prepare("SELECT p.*, c.category_name, u.username as responsible_name 
                             FROM properties p 
                             LEFT JOIN categories c ON p.category_id = c.id 
                             LEFT JOIN users u ON p.responsible_user_id = u.id 
                             WHERE p.id = ?");
$asset_stmt->execute([$asset_id]);
$asset = $asset_stmt->fetch();
if (!$asset) { header("Location: inventory.php"); exit(); }

// Fetch repair records
$repair_stmt = $pdo->prepare("SELECT * FROM repair_records WHERE asset_id = ? ORDER BY repair_datetime DESC");
$repair_stmt->execute([$asset_id]);

// Fetch assignment history (checkout/return)
$assign_stmt = $pdo->prepare("SELECT * FROM asset_assignments WHERE asset_id = ? ORDER BY checkout_datetime DESC");
$assign_stmt->execute([$asset_id]);

// ENHANCED QR CODE: Link directly to this asset's detail page
$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$asset_url = $base_url . "/asset_detail.php?id=" . $asset['id'];
$qr_url = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($asset_url);
?>

<div class="main-content">
    <div class="mb-4">
        <a href="inventory.php" class="text-decoration-none text-secondary small"><i class="bi bi-arrow-left"></i> Back to Inventory</a>
        <h2 class="fw-bold mt-2">Asset Details & History</h2>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="inventory-table-card p-4 mb-4">
                <h5 class="fw-bold border-bottom pb-2">📄 General Information</h5>
                <table class="table table-sm">
                    <tr><th>Property Code</th><td><?= htmlspecialchars($asset['property_code']) ?></td></tr>
                    <tr><th>Serial Number</th><td><code><?= htmlspecialchars($asset['serial_number']) ?></code></td></tr>
                    <tr><th>Item Name</th><td><?= htmlspecialchars($asset['item_name']) ?></td></tr>
                    <tr><th>Category</th><td><?= htmlspecialchars($asset['category_name']) ?></td></tr>
                    <tr><th>Assigned Office</th><td><?= htmlspecialchars($asset['assigned_office'] ?? '—') ?></td></tr>
                    <tr><th>Officer In-Charge</th><td><?= htmlspecialchars($asset['officer_in_charge'] ?? '—') ?></td></tr>
                    <tr><th>System Assignee</th><td><?= htmlspecialchars($asset['responsible_name'] ?? 'Unassigned') ?></td></tr>
                    <tr><th>Value (₱)</th><td><?= number_format($asset['value'], 2) ?></td></tr>
                    <tr><th>Purchase Date</th><td><?= date('M d, Y', strtotime($asset['purchase_date'])) ?></td></tr>
                    <tr><th>Status</th><td><span class="badge bg-primary"><?= $asset['status'] ?></span></td></tr>
                    <tr>
                        <th>QR Code</th>
                        <td><img src="<?= $qr_url ?>" alt="QR Code" style="width:120px;"><br>
                            <small class="text-muted">Scan to open asset page</small>
                         </td>
                    </tr>
                </table>
                <?php if ($_SESSION['role'] === 'Admin'): ?>
                <div class="mt-3">
                    <a href="checkout.php?id=<?= $asset['id'] ?>" class="btn btn-sm btn-primary">✏️ Check Out Asset</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="inventory-table-card p-4">
                <h5 class="fw-bold border-bottom pb-2">🔧 Add Repair Record <?php if($_SESSION['role'] === 'Admin'): ?><small class="text-muted">(Admin only)</small><?php endif; ?></h5>
                <?php if($_SESSION['role'] === 'Admin'): ?>
                <form action="process_repair.php" method="POST">
                    <input type="hidden" name="asset_id" value="<?= $asset['id'] ?>">
                    <div class="mb-2">
                        <label class="small fw-bold">Date & Time</label>
                        <input type="datetime-local" name="repair_datetime" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Description of Repair</label>
                        <textarea name="description" rows="2" class="form-control form-control-sm" required></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Cost (₱)</label>
                        <input type="number" step="0.01" name="cost" class="form-control form-control-sm" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary mt-2">Log Repair</button>
                </form>
                <?php else: ?>
                <p class="text-muted small">Only administrators can record repair history.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Repair History -->
    <div class="inventory-table-card p-4 mt-3">
        <h5 class="fw-bold border-bottom pb-2">🛠️ Repair History</h5>
        <?php if($repair_stmt->rowCount() > 0): ?>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="bg-light">
                    <tr><th>Date & Time</th><th>Description</th><th>Cost (₱)</th></tr>
                </thead>
                <tbody>
                <?php while($repair = $repair_stmt->fetch()): ?>
                    <tr>
                        <td><?= date('M d, Y h:i A', strtotime($repair['repair_datetime'])) ?></td>
                        <td><?= htmlspecialchars($repair['description']) ?></td>
                        <td class="text-danger fw-bold"><?= number_format($repair['cost'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-muted">No repair records found for this asset.</p>
        <?php endif; ?>
    </div>

    <!-- Assignment History (Check-in/Check-out) -->
    <div class="inventory-table-card p-4 mt-3">
        <h5 class="fw-bold border-bottom pb-2">📋 Assignment History (Check-out / Return)</h5>
        <?php if($assign_stmt->rowCount() > 0): ?>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="bg-light">
                    <tr><th>Checked Out</th><th>Assigned To</th><th>Office</th><th>Expected Return</th><th>Actual Return</th><th>Status</th><th>Notes</th></tr>
                </thead>
                <tbody>
                <?php while($assign = $assign_stmt->fetch()): ?>
                    <tr>
                        <td><?= date('M d, Y h:i A', strtotime($assign['checkout_datetime'])) ?></td>
                        <td><?= htmlspecialchars($assign['assigned_to']) ?></td>
                        <td><?= htmlspecialchars($assign['assigned_office'] ?? '—') ?></td>
                        <td><?= $assign['expected_return_date'] ? date('M d, Y', strtotime($assign['expected_return_date'])) : '—' ?></td>
                        <td><?= $assign['actual_return_datetime'] ? date('M d, Y h:i A', strtotime($assign['actual_return_datetime'])) : '—' ?></td>
                        <td>
                            <?php if($assign['status'] == 'checked_out'): ?>
                                <span class="badge bg-warning">Checked Out</span>
                                <?php if ($_SESSION['role'] === 'Admin'): ?>
                                    <a href="checkin.php?assignment_id=<?= $assign['id'] ?>&asset_id=<?= $asset['id'] ?>" class="btn btn-sm btn-success ms-2">Return</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-secondary">Returned</span>
                            <?php endif; ?>
                         </td>
                        <td><?= htmlspecialchars($assign['checkout_notes'] ?? '') ?><?= $assign['return_notes'] ? '<br><small>Return: '.htmlspecialchars($assign['return_notes']).'</small>' : '' ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-muted">No assignment history for this asset.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>