<?php 
// 1. Identify the current filename for active state highlighting
$current_page = basename($_SERVER['PHP_SELF']); 

// 2. Standardize role for comparison (Case-Insensitive)
// This ensures 'Admin', 'admin', and 'ADMIN' all work correctly.
$user_role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
?>

<div class="sidebar">
    <div class="mb-5 ps-3">
        <h4 class="text-primary fw-bold m-0">ASSET<span class="text-white">FLOW</span></h4>
        <small class="text-secondary" style="font-size: 0.7rem;">v1.0 Corporate Edition</small>
    </div>
    
    <nav class="nav flex-column gap-2">
        <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : 'text-secondary'; ?>" href="index.php">
            <i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span>
        </a>
        
        <a class="nav-link <?php echo ($current_page == 'inventory.php' || $current_page == 'edit_asset.php' || $current_page == 'add_asset.php') ? 'active' : 'text-secondary'; ?>" href="inventory.php">
            <i class="bi bi-box-seam"></i> <span>Inventory</span>
        </a>
        
        <a class="nav-link <?php echo ($current_page == 'reports.php') ? 'active' : 'text-secondary'; ?>" href="reports.php">
            <i class="bi bi-clipboard-data"></i> <span>Reports</span>
        </a>
        
        <?php if ($user_role === 'admin'): ?>
            <hr class="text-secondary opacity-25 my-2">
            
            <a class="nav-link <?php echo ($current_page == 'users.php' || $current_page == 'add_user.php') ? 'active' : 'text-secondary'; ?>" href="users.php">
                <i class="bi bi-person-gear"></i> <span>User Access</span>
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'history.php') ? 'active' : 'text-secondary'; ?>" href="history.php">
                <i class="bi bi-clock-history"></i> <span>System Logs</span>
            </a>
        <?php endif; ?>
        
        <hr class="text-secondary opacity-25 mt-4">
        
        <a class="nav-link text-danger" href="logout.php">
            <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
        </a>
    </nav>
</div>