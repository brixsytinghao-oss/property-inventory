<?php 
// 1. Session and Role Security
session_start();

// Redirect to login if not authenticated at all
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ROLE GATE: Redirect to dashboard if the user is NOT an admin
// Uses strtolower to ensure 'Admin' or 'admin' from the DB both pass
if (strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php?error=unauthorized"); 
    exit();
}

// 2. Load dependencies
include 'db_connect.php'; 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// 3. Fetch all system users from the database
$stmt = $pdo->query("SELECT id, username, role, last_login FROM users ORDER BY username ASC");
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0">User Access Control</h2>
            <p class="text-muted small">Manage system administrators and staff permissions.</p>
        </div>
        <a href="add_user.php" class="btn btn-primary btn-action px-4 shadow-sm">
            <i class="bi bi-person-plus"></i> Add User
        </a>
    </div>

    <div class="inventory-table-card shadow-sm border-0 rounded-3 overflow-hidden">
        <table class="table table-hover align-middle mb-0 bg-white">
            <thead class="bg-light">
                <tr class="text-muted small text-uppercase">
                    <th class="ps-4 py-3">Username</th>
                    <th class="py-3">Role</th>
                    <th class="py-3">Last Login</th>
                    <th class="text-end pe-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $stmt->fetch()): ?>
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($user['username']); ?></div>
                    </td>
                    <td>
                        <span class="badge rounded-pill <?php echo (strtolower($user['role']) == 'admin') ? 'bg-primary' : 'bg-secondary'; ?> px-3 py-2">
                            <?php echo $user['role']; ?>
                        </span>
                    </td>
                    <td class="text-muted">
                        <?php echo $user['last_login'] ? date('M d, Y h:i A', strtotime($user['last_login'])) : 'Never'; ?>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group gap-2">
                            <a href="edit_user_permissions.php?id=<?php echo $user['id']; ?>" class="btn btn-edit btn-action" title="Change Permissions">
                                <i class="bi bi-shield-lock"></i>
                            </a>
                            
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" 
                                   class="btn btn-edit btn-action text-danger" 
                                   title="Delete User" 
                                   onclick="return confirm('Are you sure you want to remove access for this user? This action cannot be undone.');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border px-3">Current User</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>