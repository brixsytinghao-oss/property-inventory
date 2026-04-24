<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $serial = $_POST['serial_number'];
    $item_name = $_POST['item_name'];
    $category_id = $_POST['category_id'];
    $office = $_POST['assigned_office'] ?? null;
    $officer = $_POST['officer_in_charge'] ?? null;
    $resp_user = !empty($_POST['responsible_user_id']) ? $_POST['responsible_user_id'] : null;
    $value = $_POST['value'];
    $purchase_date = $_POST['purchase_date'];
    $status = $_POST['status'];

    try {
        $code_query = $pdo->prepare("SELECT property_code FROM properties WHERE id = ?");
        $code_query->execute([$id]);
        $property_code = $code_query->fetchColumn();

        $sql = "UPDATE properties SET 
                serial_number = ?,
                item_name = ?, 
                category_id = ?, 
                assigned_office = ?,
                officer_in_charge = ?,
                responsible_user_id = ?,
                value = ?, 
                purchase_date = ?, 
                status = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$serial, $item_name, $category_id, $office, $officer, $resp_user, $value, $purchase_date, $status, $id]);

        $log_sql = "INSERT INTO history_log (user_id, action_type, property_code, details) 
                    VALUES (?, 'Edit', ?, ?)";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([$_SESSION['user_id'], $property_code, "Updated details for property: $item_name"]);

        header("Location: inventory.php?status=updated");
        exit();
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
} else {
    header("Location: inventory.php");
    exit();
}