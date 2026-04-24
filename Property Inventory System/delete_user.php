<?php
session_start();

// Security: Only admin can delete users
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

include 'db_connect.php';

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Prevent admin from deleting themselves
if ($user_id == $_SESSION['user_id']) {
    header("Location: users.php?error=cantdeleteyourself");
    exit();
}

try {
    // Fetch username before deletion for logging
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        header("Location: users.php?error=usernotfound");
        exit();
    }
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Delete the user
    $delete = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $delete->execute([$user_id]);
    
    // Log the deletion
    $log_sql = "INSERT INTO history_log (user_id, action_type, property_code, details) 
                VALUES (?, 'UserDelete', ?, ?)";
    $log_stmt = $pdo->prepare($log_sql);
    $log_stmt->execute([
        $_SESSION['user_id'],
        'USER_MGT',
        "Deleted user account: " . $user['username']
    ]);
    
    $pdo->commit();
    header("Location: users.php?status=userdeleted");
    exit();
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("Delete user error: " . $e->getMessage());
    header("Location: users.php?error=deletefailed");
    exit();
}
?>