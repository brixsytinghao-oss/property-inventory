<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY & ROLE GATE
 * We use strtolower() to ensure 'Admin', 'admin', and 'ADMIN' are all recognized.
 */
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    // Redirect unauthorized users back to the inventory with an error flag
    header("Location: inventory.php?error=unauthorized");
    exit();
}

/**
 * 2. DELETE PROCESSING WITH AUDIT LOGGING
 */
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Start a database transaction to ensure Atomicity (All or Nothing)
        $pdo->beginTransaction();

        // A. Fetch details first (needed for the history log before the record is destroyed)
        $check = $pdo->prepare("SELECT property_code, item_name FROM properties WHERE id = ?");
        $check->execute([$id]);
        $asset = $check->fetch();

        if ($asset) {
            // B. Perform the Deletion from the 'properties' table
            $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
            $stmt->execute([$id]);

            // C. Insert the "Delete" action into the 'history_log'
            // This creates a paper trail of who deleted what and when.
            $log_sql = "INSERT INTO history_log (user_id, action_type, property_code, details) 
                        VALUES (?, 'Delete', ?, ?)";
            $log_stmt = $pdo->prepare($log_sql);
            $log_stmt->execute([
                $_SESSION['user_id'], 
                $asset['property_code'], 
                "Permanently removed asset: " . $asset['item_name']
            ]);

            // D. Commit: Finalize both the deletion and the log entry
            $pdo->commit();
            header("Location: inventory.php?status=deleted");
            exit();
        } else {
            // If the record doesn't exist (e.g., already deleted by someone else)
            header("Location: inventory.php?error=notfound");
            exit();
        }

    } catch (PDOException $e) {
        // Rollback: If the log fails, don't delete the asset (and vice-versa)
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Log the technical error for debugging
        error_log("Delete Error: " . $e->getMessage());
        die("Critical Database Error. Please contact the system administrator.");
    }
} else {
    // Redirect if the script is accessed directly without an ID parameter
    header("Location: inventory.php");
    exit();
}