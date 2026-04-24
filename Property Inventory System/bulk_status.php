<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: inventory.php?error=unauthorized");
    exit();
}
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['asset_ids']) && isset($_POST['new_status'])) {
    $ids = $_POST['asset_ids'];
    $status = $_POST['new_status'];
    
    if (empty($ids)) {
        header("Location: inventory.php?error=no_selection");
        exit();
    }
    
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("UPDATE properties SET status = ? WHERE id IN ($placeholders)");
    $params = array_merge([$status], $ids);
    $stmt->execute($params);
    
    // Log the bulk action
    $log = $pdo->prepare("INSERT INTO history_log (user_id, action_type, property_code, details) VALUES (?, 'BulkEdit', 'MULTIPLE', ?)");
    $log->execute([$_SESSION['user_id'], "Bulk status changed to $status for " . count($ids) . " assets"]);
    
    header("Location: inventory.php?msg=bulk_updated");
} else {
    header("Location: inventory.php");
}
exit();