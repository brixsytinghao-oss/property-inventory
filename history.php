<?php 
session_start();
include 'db_connect.php'; 

/**
 * 1. IMPROVED SECURITY GATE
 * This check looks for the word "admin" anywhere in the role string, 
 * matching "Admin", "admin", or "Admin (Full System Access)".
 */
$is_admin = false;
if (isset($_SESSION['role'])) {
    $current_role = strtolower($_SESSION['role']);
    if (strpos($current_role, 'admin') !== false) {
        $is_admin = true;
    }
}

// Redirect if not an admin instead of just showing a white screen
if (!isset($_SESSION['user_id']) || !$is_admin) { 
    header("Location: index.php?error=unauthorized");
    exit();
}

include 'includes/header.php'; 
include 'includes/sidebar.php'; 

/**
 * 2. FETCH SYSTEM ACTIVITY
 */
$query = "SELECT h.*, u.username FROM history_log h 
          JOIN users u ON h.user_id = u.id 
          ORDER BY h.created_at DESC";
$stmt = $pdo->query($query);
?>

<div class="main-content">
    <div class="mb-4">
        <h2 class="fw-bold m-0">System Activity History</h2>
        <p class="text-muted">Audit trail of all property additions, modifications, and deletions.</p>
    </div>

    <div class="inventory-table-card shadow-sm border-0 rounded-3 overflow-hidden">
        <table class="table table-hover align-middle mb-0 bg-white">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Timestamp</th>
                    <th class="py-3">User</th>
                    <th class="py-3">Action</th>
                    <th class="py-3">Property Code</th>
                    <th class="py-3">Details</th>
                </tr>
            </thead>
            <tbody>
                <?php while($log = $stmt->fetch()): ?>
                <tr>
                    <td class="ps-4 text-muted small">
                        <?= date('M d, Y h:i A', strtotime($log['created_at'])) ?>
                    </td>
                    
                    <td>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($log['username']) ?></span>
                    </td>
                    
                    <td>
                        <?php 
                            $badge_class = 'bg-primary'; // Default (Update)
                            if ($log['action_type'] == 'Add') $badge_class = 'bg-success';
                            if ($log['action_type'] == 'Delete') $badge_class = 'bg-danger';
                        ?>
                        <span class="badge rounded-pill <?= $badge_class ?> px-3 py-2">
                            <?= $log['action_type'] ?>
                        </span>
                    </td>
                    
                    <td class="fw-bold text-primary">
                        <code><?= htmlspecialchars($log['property_code']) ?></code>
                    </td>
                    
                    <td class="text-secondary small">
                        <?= htmlspecialchars($log['details']) ?>
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