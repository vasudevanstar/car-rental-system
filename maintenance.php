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
        body { background: #fdfdfd; }
        .m-card { border-radius: 12px; border: 1px solid #eee; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
        .m-header { background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-5">
    <div class="container">
        <span class="navbar-brand fw-bold font-monospace"><i class="bi bi-tools me-2"></i> FLEET MAINTENANCE</span>
        <div class="d-flex align-items-center">
            <span class="text-white me-3 small opacity-75"><?php echo $_SESSION['user']['name']; ?></span>
            <a href="javascript:void(0)" onclick="logout()" class="btn btn-outline-light btn-sm">Sign Out</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                <h4 class="fw-bold mb-4">Log Repair Task</h4>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> small mb-4">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="action" value="log_repair">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Vehicle</label>
                        <select name="vehicleId" class="form-select border-0 bg-light" required>
                            <option value="">Choose...</option>
                            <?php foreach ($vehicles as $v): ?>
                                <option value="<?php echo $v['id']; ?>"><?php echo $v['brand'] . ' ' . $v['name']; ?> (<?php echo $v['license_plate']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Category</label>
                        <select name="category" class="form-select border-0 bg-light" required>
                            <option value="General Cleaning">General Cleaning</option>
                            <option value="Oil Change">Oil Change</option>
                            <option value="Tire Rotation">Tire Rotation</option>
                            <option value="Brake Service">Brake Service</option>
                            <option value="Engine Check">Engine Check</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Service Notes</label>
                        <textarea name="description" class="form-control border-0 bg-light" rows="3" placeholder="Describe the work done..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Cost ($)</label>
                        <input type="number" step="0.01" name="cost" class="form-control border-0 bg-light" value="0.00">
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="markAvailable" id="markAvailable" checked>
                        <label class="form-check-label small" for="markAvailable">
                            Mark vehicle as Available after logging
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow">
                        Submit Log Entry
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <h4 class="fw-bold mb-4">Current Fleet Status</h4>
            <div class="table-responsive bg-white rounded-4 shadow-sm border">
                <table class="table align-middle mb-0">
                    <thead class="text-white" style="background: #4b5563;">
                        <tr><th class="ps-4">Vehicle</th><th>Spec</th><th>Status</th><th class="text-end pe-4">Metrics</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($vehicles as $v): ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold"><?php echo $v['brand'] . ' ' . $v['name']; ?></div>
                                <div class="small text-muted">ID: #<?php echo $v['id']; ?></div>
                            </td>
                            <td>
                                <div class="badge bg-light text-dark border fw-normal"><?php echo $v['license_plate']; ?></div>
                                <div class="small text-muted mt-1"><?php echo $v['color']; ?></div>
                            </td>
                            <td>
                                <span class="badge rounded-pill <?php echo $v['status'] === 'Maintenance' ? 'bg-danger' : 'bg-success'; ?>">
                                    <?php echo $v['status']; ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="small fw-bold">Odo: <?php echo number_format($v['odometer']); ?> mi</div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-info" style="width: <?php echo $v['fuel_level']; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="mt-4 text-muted small"><i class="bi bi-info-circle me-1"></i> Showing vehicles in maintenance and those available for routing.</p>
        </div>
    </div>
</div>

</body>
</html>
