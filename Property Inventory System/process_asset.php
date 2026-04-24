<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: inventory.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code   = $_POST['property_code'] ?? '';
    $serial = $_POST['serial_number'] ?? '';
    $name   = $_POST['item_name'] ?? '';
    $cat_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
    $office = $_POST['assigned_office'] ?? null;
    $officer = $_POST['officer_in_charge'] ?? null;
    $resp_user = !empty($_POST['responsible_user_id']) ? $_POST['responsible_user_id'] : null;
    $value  = !empty($_POST['value']) ? $_POST['value'] : 0;
    $date   = !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : date('Y-m-d');
    $status = $_POST['status'] ?? 'Available';

    if (empty($code) || empty($serial) || empty($name)) {
        die("Error: Property Code, Serial Number and Item Name are required.");
    }

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO properties (property_code, serial_number, item_name, category_id, assigned_office, officer_in_charge, responsible_user_id, value, purchase_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$code, $serial, $name, $cat_id, $office, $officer, $resp_user, $value, $date, $status]);

        $log_sql = "INSERT INTO history_log (user_id, action_type, property_code, details) 
                    VALUES (?, 'Add', ?, ?)";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([$_SESSION['user_id'], $code, "Added new property: $name (SN: $serial)"]);

        $pdo->commit();
        header("Location: inventory.php?msg=added");
        exit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        
        // Check for duplicate serial number error (MySQL error code 1062)
        if ($e->errorInfo[1] == 1062) {
            $error_msg = "Duplicate entry: The serial number '$serial' already exists. Please use a unique serial number.";
            header("Location: add_asset.php?error=" . urlencode($error_msg));
            exit();
        } else {
            die("Database Error: " . $e->getMessage());
        }
    }
} else {
    header("Location: inventory.php");
    exit();
}