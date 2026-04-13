<?php
// maintenance.php
require_once __DIR__ . '/config/db.php';

// Check role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'maintenance') {
    header('Location: login.php?redirect=maintenance.php');
    exit;
}

$staffId = $_SESSION['user']['id'];
$message = '';
$messageType = 'info';

// Handle Logging (Pure PHP POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'log_repair') {
        $vehicleId = (int)$_POST['vehicleId'];
        $category = $_POST['category'];
        $desc = $_POST['description'];
        $cost = (float)$_POST['cost'];

        try {
            // 1. Create Log
            $stmt = $pdo->prepare("INSERT INTO maintenance_logs (vehicle_id, staff_id, category, description, cost) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$vehicleId, $staffId, $category, $desc, $cost]);

            // 2. Clear Maintenance Status if done
            if (isset($_POST['markAvailable'])) {
                $vStmt = $pdo->prepare("UPDATE vehicles SET status = 'Available' WHERE id = ?");
                $vStmt->execute([$vehicleId]);
            }

            $message = "Maintenance task logged successfully!";
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Fetch Vehicles in Maintenance
try {
    $stmt = $pdo->query("SELECT * FROM vehicles WHERE status = 'Maintenance' OR status = 'Available' ORDER BY status DESC, name ASC");
    $vehicles = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fleet Maintenance - FastRide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --accent: #4f46e5; --bg: #f3f4f6; --glass-bg: rgba(255, 255, 255, 0.95); }
        body { background: var(--bg); font-family: 'Inter', sans-serif; }
        .glass-panel { background: var(--glass-bg); backdrop-filter: blur(10px); border: 1px solid rgba(0,0,0,0.05); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .table thead { background: #f9fafb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark py-3 px-4 shadow-sm mb-5">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#"><i class="bi bi-tools me-2 text-primary"></i> FLEET MAINTENANCE</a>
        <div class="ms-auto d-flex align-items-center">
            <span class="text-white-50 small me-3 d-none d-md-block">Signed in as <strong><?php echo $_SESSION['user']['name']; ?></strong></span>
            <a href="javascript:void(0)" onclick="logout()" class="btn btn-outline-danger btn-sm rounded-pill px-3">Logout</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="glass-panel p-4 sticky-lg-top" style="top: 100px;">
                <h4 class="fw-bold mb-4">Log Repair Task</h4>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show small mb-4 rounded-3 border-0 shadow-sm">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="action" value="log_repair">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted"><i class="bi bi-car-front me-1"></i> Select Vehicle</label>
                        <select name="vehicleId" class="form-select border-0 bg-light py-2 shadow-none" style="border-radius: 10px;" required>
                            <option value="">Choose...</option>
                            <?php foreach ($vehicles as $v): ?>
                                <option value="<?php echo $v['id']; ?>"><?php echo $v['brand'] . ' ' . $v['name']; ?> (<?php echo $v['license_plate']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted"><i class="bi bi-tag me-1"></i> Category</label>
                        <select name="category" class="form-select border-0 bg-light py-2 shadow-none" style="border-radius: 10px;" required>
                            <option value="General Cleaning">General Cleaning</option>
                            <option value="Oil Change">Oil Change</option>
                            <option value="Tire Rotation">Tire Rotation</option>
                            <option value="Brake Service">Brake Service</option>
                            <option value="Engine Check">Engine Check</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted"><i class="bi bi-pencil-square me-1"></i> Service Notes</label>
                        <textarea name="description" class="form-control border-0 bg-light py-2 shadow-none" style="border-radius: 10px;" rows="3" placeholder="Describe the work done..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted"><i class="bi bi-currency-dollar me-1"></i> Cost</label>
                        <input type="number" step="0.01" name="cost" class="form-control border-0 bg-light py-2 shadow-none" style="border-radius: 10px;" value="0.00">
                    </div>
                    <div class="form-check mb-4 mt-2">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="markAvailable" id="markAvailable" checked>
                        <label class="form-check-label small text-muted" for="markAvailable">
                            Ready for immediate rent (Mark as Available)
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                        Submit Log Entry <i class="bi bi-arrow-right-short ms-1"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold m-0">Global Fleet Status</h4>
                <div class="badge bg-white text-dark border p-2 px-3 rounded-pill shadow-sm"><i class="bi bi-circle-fill text-success small me-1"></i> Live Update</div>
            </div>
            <div class="glass-panel overflow-hidden">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr><th class="ps-4">Vehicle Identity</th><th>Tech Specs</th><th>Live Status</th><th class="text-end pe-4">Current Metrics</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($vehicles as $v): ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark"><?php echo $v['brand'] . ' ' . $v['name']; ?></div>
                                    <div class="small text-muted font-monospace">UID: #<?php echo $v['id']; ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold small"><?php echo $v['license_plate']; ?></div>
                                    <div class="small text-muted"><?php echo $v['color']; ?> • <?php echo $v['type']; ?></div>
                                </td>
                                <td>
                                    <?php if($v['status'] === 'Maintenance'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border-0 p-2 px-3 rounded-pill fw-bold" style="font-size:0.7rem;">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Maintenance
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border-0 p-2 px-3 rounded-pill fw-bold" style="font-size:0.7rem;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Available
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="small fw-bold text-dark"><?php echo number_format($v['odometer']); ?> mi</div>
                                    <div class="d-flex align-items-center justify-content-end gap-2 mt-1">
                                        <div class="progress flex-grow-1" style="height: 4px; width: 60px;">
                                            <div class="progress-bar bg-primary" style="width: <?php echo $v['fuel_level']; ?>%"></div>
                                        </div>
                                        <span class="small opacity-50"><?php echo $v['fuel_level']; ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="mt-3 text-muted small px-2"><i class="bi bi-info-circle me-1"></i> Dashboard synchronized with real-time rental availability and workshop scheduling.</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="frontend/script.js"></script>
</body>
</html>
