<div class="sidebar">
    <h4 class="mb-5 text-primary fw-bold">ASSET<span class="text-white">FLOW</span></h4>
    <nav class="nav flex-column gap-2">
        <a class="nav-link text-white active bg-primary rounded" href="#"><i class="bi bi-grid"></i> Dashboard</a>
        <a class="nav-link text-secondary" href="#"><i class="bi bi-box"></i> Inventory</a>
        <a class="nav-link text-secondary" href="#"><i class="bi bi-bar-chart"></i> Reports</a>
        <a class="nav-link text-secondary" href="#"><i class="bi bi-gear"></i> Settings</a>
    </nav>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Property Overview</h2>
            <p class="text-muted">Manage and track company assets in real-time.</p>
        </div>
        <button class="btn btn-primary px-4 py-2 fw-bold shadow-sm">+ Add New Asset</button>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card p-3">
                <small class="text-muted fw-bold">TOTAL ASSETS</small>
                <h3 class="fw-bold">1,240</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 border-start border-success border-4">
                <small class="text-muted fw-bold">AVAILABLE</small>
                <h3 class="fw-bold text-success">850</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 border-start border-warning border-4">
                <small class="text-muted fw-bold">IN MAINTENANCE</small>
                <h3 class="fw-bold text-warning">12</h3>
            </div>
        </div>
    </div>

    <div class="inventory-table-card">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Asset Code</th>
                    <th>Item Details</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Acquisition</th>
                    <th class="text-end pe-4">Management</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="ps-4 fw-bold text-primary">#PROP-2026-001</td>
                    <td>
                        <div class="fw-bold">MacBook Pro M3</div>
                        <small class="text-muted">IT Dept / High Performance</small>
                    </td>
                    <td><span class="badge bg-light text-dark">Hardware</span></td>
                    <td><span class="status-badge bg-success-subtle text-success">In Use</span></td>
                    <td>Jan 15, 2026</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-primary">View</button>
                        <button class="btn btn-sm btn-light border">Edit</button>
                    </td>
                </tr>
                </tbody>
        </table>
    </div>
</div>