<?php
session_start();

// Security: Only admin can access
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

include 'db_connect.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Prevent admin from editing own role through this page (optional safety)
if ($user_id == $_SESSION['user_id']) {
    header("Location: users.php?error=cantselfedit");
    exit();
}

// Fetch user data
$stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: users.php?error=usernotfound");
    exit();
}

// Process role update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_role = $_POST['role'];
    
    try {
        $update = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $update->execute([$new_role, $user_id]);
        
        // Log the permission change
        $log_sql = "INSERT INTO history_log (user_id, action_type, property_code, details) 
                    VALUES (?, 'Permission', ?, ?)";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([
            $_SESSION['user_id'],
            'USER_MGT',
            "Changed role of user '{$user['username']}' to {$new_role}"
        ]);
        
        header("Location: users.php?status=roleupdated");
        exit();
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<div class="main-content">
    <div class="mb-4">
        <a href="users.php" class="text-decoration-none text-secondary small">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
        <h2 class="fw-bold mt-2">Change User Permissions</h2>
        <p class="text-muted">Editing: <strong><?= htmlspecialchars($user['username']) ?></strong></p>
    </div>

    <div class="inventory-table-card p-5" style="max-width: 600px;">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-4">
                <label class="form-label fw-bold small">System Role</label>
                <select name="role" class="form-select" required>
                    <option value="Staff" <?= $user['role'] == 'Staff' ? 'selected' : '' ?>>Staff (View & Edit Inventory)</option>
                    <option value="Admin" <?= $user['role'] == 'Admin' ? 'selected' : '' ?>>Admin (Full System Access)</option>
                </select>
                <div class="form-text text-muted small">
                    <i class="bi bi-info-circle"></i> Staff can view/edit inventory but cannot access user management or system logs.
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="bi bi-save"></i> Update Permission
                </button>
                <a href="users.php" class="btn btn-light px-4 py-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>