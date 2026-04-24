<?php
session_start();
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $asset_id = $_POST['asset_id'];
    $repair_datetime = $_POST['repair_datetime'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];

    try {
        $stmt = $pdo->prepare("INSERT INTO repair_records (asset_id, repair_datetime, description, cost) VALUES (?, ?, ?, ?)");
        $stmt->execute([$asset_id, $repair_datetime, $description, $cost]);

        $log = $pdo->prepare("INSERT INTO history_log (user_id, action_type, property_code, details) VALUES (?, 'Repair', ?, ?)");
        $prop_code = $pdo->prepare("SELECT property_code FROM properties WHERE id = ?");
        $prop_code->execute([$asset_id]);
        $code = $prop_code->fetchColumn();
        $log->execute([$_SESSION['user_id'], $code, "Repair logged: $description (₱$cost)"]);

        header("Location: asset_detail.php?id=$asset_id&repair=added");
    } catch (PDOException $e) {
        die("Error adding repair: " . $e->getMessage());
    }
} else {
    header("Location: inventory.php");
}