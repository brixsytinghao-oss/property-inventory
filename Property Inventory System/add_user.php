<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php'; 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 
?>

<div class="main-content">
    <div class="mb-4">
        <a href="users.php" class="text-decoration-none small text-secondary">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
        <h2 class="fw-bold mt-2">Add New System User</h2>
    </div>

    <div class="inventory-table-card p-5" style="max-width: 600px;">
        <form action="process_user.php" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold small">Username</label>
                <input type="text" name="username" class="form-control" placeholder="e.g. jdoe_staff" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold small">Temporary Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small">System Role</label>
                <select name="role" class="form-select" required>
                    <option value="Staff">Staff (View & Edit Inventory)</option>
                    <option value="Admin">Admin (Full System Access)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-action px-5 py-2">
                <i class="bi bi-check-lg"></i> Create Account
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>