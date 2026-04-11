<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    
    // 1. Capture all form data
    $id = $_POST['id'];
    $item_name = $_POST['item_name'];
    $category_id = $_POST['category_id'];
    $value = $_POST['value'];
    $purchase_date = $_POST['purchase_date'];
    $status = $_POST['status'];

    try {
        // 2. Fetch the Property Code first (needed for the history log)
        $code_query = $pdo->prepare("SELECT property_code FROM properties WHERE id = ?");
        $code_query->execute([$id]);
        $property_code = $code_query->fetchColumn();

        // 3. Update the Asset Details
        $sql = "UPDATE properties SET 
                item_name = ?, 
                category_id = ?, 
                value = ?, 
                purchase_date = ?, 
                status = ? 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$item_name, $category_id, $value, $purchase_date, $status, $id])) {
            
            // 4. INSERT INTO HISTORY LOG (The Combination)
            $log_sql = "INSERT INTO history_log (user_id, action_type, property_code, details) 
                        VALUES (?, ?, ?, ?)";
            $log_stmt = $pdo->prepare($log_sql);
            $log_stmt->execute([
                $_SESSION['user_id'], 
                'Edit', 
                $property_code, 
                "Updated details for property: $item_name"
            ]);

            // Redirect with success message
            header("Location: inventory.php?status=updated");
        } else {
            echo "Error: Could not update the property record.";
        }
        exit();

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
} else {
    header("Location: inventory.php");
    exit();
}