<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db_connect.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$asset_id = $_GET['id'] ?? 0;
$asset = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$asset->execute([$asset_id]);
$asset = $asset->fetch();
if (!$asset) { header("Location: inventory.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assigned_to = $_POST['assigned_to'];
    $assigned_office = $_POST['assigned_office'];
    $expected_return = $_POST['expected_return_date'] ?: null;
    $notes = $_POST['notes'];

    $stmt = $pdo->prepare("INSERT INTO asset_assignments 
        (asset_id, assigned_to, assigned_office, checkout_datetime, expected_return_date, checkout_notes, created_by, status)
        VALUES (?, ?, ?, NOW(), ?, ?, ?, 'checked_out')");
    $stmt->execute([$asset_id, $assigned_to, $assigned_office, $expected_return, $notes, $_SESSION['user_id']]);

    // Optionally update asset status to 'In Use'
    $pdo->prepare("UPDATE properties SET status = 'In Use' WHERE id = ?")->execute([$asset_id]);

    header("Location: asset_detail.php?id=$asset_id&msg=checkedout");
    exit();
}
?>
<div class="main-content">
    <h2>Check Out Asset: <?= htmlspecialchars($asset['item_name']) ?></h2>
    <form method="POST">
        <div class="mb-3"><label>Assigned To (Officer Name)</label><input name="assigned_to" class="form-control" required></div>
        <div class="mb-3"><label>Assigned Office</label><input name="assigned_office" class="form-control"></div>
        <div class="mb-3"><label>Expected Return Date</label><input type="date" name="expected_return_date" class="form-control"></div>
        <div class="mb-3"><label>Notes</label><textarea name="notes" class="form-control"></textarea></div>
        <button type="submit" class="btn btn-primary">Confirm Checkout</button>
        <a href="asset_detail.php?id=<?= $asset_id ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>