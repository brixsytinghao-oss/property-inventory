<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY & ROLE CHECK
 * Only logged-in users with the 'Admin' role can proceed.
 * This prevents unauthorized staff from adding assets.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: inventory.php?error=unauthorized");
    exit();
}

/**
 * 2. FORM PROCESSING
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize data using null coalescing
    $code   = $_POST['property_code'] ?? '';
    $name   = $_POST['item_name'] ?? '';
    $cat_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
    $value  = !empty($_POST['value']) ? $_POST['value'] : 0;
    $date   = !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : date('Y-m-d');
    $status = $_POST['status'] ?? 'Available';

    // Basic Validation: Ensure mandatory fields are not empty
    if (empty($code) || empty($name)) {
        die("Error: Property Code and Item Name are required fields.");
    }

    try {
        /**
         * 3. DATABASE TRANSACTION
         * This ensures that both the asset entry and the activity log succeed.
         * If the log fails, the asset won't be added (Rollback).
         */
        $pdo->beginTransaction();

        // A. Insert the Asset into the properties table
        $sql = "INSERT INTO properties (property_code, item_name, category_id, value, purchase_date, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$code, $name, $cat_id, $value, $date, $status]);

        // B. Log the "Add" Action to history_log for the audit trail
        $log_sql = "INSERT INTO history_log (user_id, action_type, property_code, details) 
                    VALUES (?, 'Add', ?, ?)";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([
            $_SESSION['user_id'], 
            $code, 
            "Added new property: $name"
        ]);

        // C. Finalize changes (Commit)
        $pdo->commit();

        // Redirect back to inventory with a success message
        header("Location: inventory.php?msg=added");
        exit();

    } catch (PDOException $e) {
        // If an error occurs (e.g., duplicate property code), undo all changes
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Output specific error for debugging
        die("Database Error: " . $e->getMessage());
    }
} else {
    // Redirect if the file is accessed directly without submitting the form
    header("Location: inventory.php");
    exit();
}