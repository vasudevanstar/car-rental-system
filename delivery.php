<?php
// delivery.php
require_once __DIR__ . '/config/db.php';

// Check role
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'delivery' && $_SESSION['user']['role'] !== 'admin')) {
    header('Location: login.php?redirect=delivery.php');
    exit;
}

$employeeId = $_SESSION['user']['id'];
$message = '';
$messageType = 'info';

// Handle All Actions (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $rentalId = (int)$_POST['rentalId'];
    $action = $_POST['action'];

    try {
        if ($action === 'delivery_update') {
            $newStatus = $_POST['status'];
            $notes = $_POST['delivery_notes'] ?? null;
            if ($newStatus === 'Delivered') {
                $stmt = $pdo->prepare("UPDATE rentals SET delivery_status = ?, delivered_at = NOW(), delivery_notes = ? WHERE id = ? AND delivery_employee_id = ?");
                $stmt->execute([$newStatus, $notes, $rentalId, $employeeId]);
            } else {
                $stmt = $pdo->prepare("UPDATE rentals SET delivery_status = ? WHERE id = ? AND delivery_employee_id = ?");
                $stmt->execute([$newStatus, $rentalId, $employeeId]);
            }
        } elseif ($action === 'claim_return') {
            $stmt = $pdo->prepare("UPDATE rentals SET return_employee_id = ?, return_status = 'Requested' WHERE id = ? AND return_employee_id IS NULL");
            $stmt->execute([$employeeId, $rentalId]);
            $message = "Return job claimed successully!";
        } elseif ($action === 'return_status_update') {
            $newStatus = $_POST['status'];
            if ($newStatus === 'Picked Up') {
                $stmt = $pdo->prepare("UPDATE rentals SET return_status = 'Picked Up' WHERE id = ? AND return_employee_id = ?");
                $stmt->execute([$rentalId, $employeeId]);
            } elseif ($newStatus === 'Completed') {
                $notes = $_POST['return_notes'] ?? '';
                $charges = (float)($_POST['return_charges'] ?? 0);
                
                // Transactional update: Rental and Vehicle
                $pdo->beginTransaction();
                
                // 1. Update Rental
                $stmt = $pdo->prepare("UPDATE rentals SET return_status = 'Completed', returned_at = NOW(), return_notes = ?, return_charges = ? WHERE id = ? AND return_employee_id = ?");
                $stmt->execute([$notes, $charges, $rentalId, $employeeId]);
                
                // 2. Fetch vehicle ID
                $vStmt = $pdo->prepare("SELECT vehicle_id FROM rentals WHERE id = ?");
                $vStmt->execute([$rentalId]);
                $vId = $vStmt->fetchColumn();
                
                // 3. Update Vehicle to Available
                $uStmt = $pdo->prepare("UPDATE vehicles SET status = 'Available' WHERE id = ?");
                $uStmt->execute([$vId]);
                
                $pdo->commit();
                $message = "Vehicle returned and marked as Available!";
            }
        }
        
        $logStmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, details) VALUES (?, ?, ?)");
        $logStmt->execute([$employeeId, "Fleet Action: " . $action, "Processed rental #$rentalId"]);
        
        if(!$message) $message = "Action processed successfully!";
        $messageType = 'success';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = "Error: " . $e->getMessage();
        $messageType = 'danger';
    }
}

// Fetch Stats
$totalDelivered = $pdo->query("SELECT COUNT(*) FROM rentals WHERE delivery_status = 'Delivered'")->fetchColumn();
$totalReturned = $pdo->query("SELECT COUNT(*) FROM rentals WHERE return_status = 'Completed'")->fetchColumn();

// Tabs Logic
$tab = $_GET['tab'] ?? 'active_delivery';

try {
    if ($tab === 'active_delivery') {
        $stmt = $pdo->prepare("SELECT r.*, c.name as customer_name, c.phone as customer_phone, v.brand, v.name as car_name, v.license_plate, v.color FROM rentals r JOIN customers c ON r.customer_email = c.email JOIN vehicles v ON r.vehicle_id = v.id WHERE r.delivery_employee_id = ? AND r.delivery_status != 'Delivered'");
        $stmt->execute([$employeeId]);
    } elseif ($tab === 'return_pool') {
        $stmt = $pdo->prepare("SELECT r.*, c.name as customer_name, v.brand, v.name as car_name, v.license_plate FROM rentals r JOIN customers c ON r.customer_email = c.email JOIN vehicles v ON r.vehicle_id = v.id WHERE r.return_status = 'Requested' AND r.return_employee_id IS NULL");
        $stmt->execute();
    } elseif ($tab === 'my_returns') {
        $stmt = $pdo->prepare("SELECT r.*, c.name as customer_name, c.phone as customer_phone, v.brand, v.name as car_name, v.license_plate, v.color FROM rentals r JOIN customers c ON r.customer_email = c.email JOIN vehicles v ON r.vehicle_id = v.id WHERE r.return_employee_id = ? AND r.return_status != 'Completed'");
        $stmt->execute([$employeeId]);
    } elseif ($tab === 'history') {
        $stmt = $pdo->prepare("SELECT r.*, c.name as customer_name, v.brand, v.name as car_name, v.license_plate, 'Delivery' as type FROM rentals r JOIN customers c ON r.customer_email = c.email JOIN vehicles v ON r.vehicle_id = v.id WHERE (r.delivery_employee_id = ? AND r.delivery_status = 'Delivered') OR (r.return_employee_id = ? AND r.return_status = 'Completed') ORDER BY r.id DESC LIMIT 20");
        $stmt->execute([$employeeId, $employeeId]);
    }
    $records = $stmt->fetchAll();
} catch (PDOException $e) { $records = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fleet Portal - FastRide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --accent: #4f46e5; --bg: #f3f4f6; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; }
        .nav-pills-custom .nav-link { color: #4b5563; font-weight: 600; border-radius: 12px; padding: 10px 20px; transition: all 0.2s; }
        .nav-pills-custom .nav-link.active { background: var(--accent); color: white; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        .card-custom { border: none; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .table thead { background: #f9fafb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge-status { font-weight: 600; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark py-3 px-4 shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#"><i class="bi bi-shield-shaded me-2"></i> FASTRIDE FLEET OPS</a>
        <div class="ms-auto d-flex align-items-center">
            <a href="javascript:void(0)" onclick="logout()" class="btn btn-outline-danger btn-sm rounded-pill px-3">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 pb-5">
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="bg-white p-4 rounded-4 shadow-sm text-center">
                <h6 class="text-muted small fw-bold mb-1">ALL DELIVERIES</h6>
                <h3 class="fw-bold mb-0"><?php echo $totalDelivered; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-white p-4 rounded-4 shadow-sm text-center border-start border-primary border-4">
                <h6 class="text-muted small fw-bold mb-1">ALL RETURNS</h6>
                <h3 class="fw-bold mb-0"><?php echo $totalReturned; ?></h3>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0">
            <i class="bi bi-info-circle-fill me-2"></i> <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <ul class="nav nav-pills nav-pills-custom gap-2 mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo $tab === 'active_delivery' ? 'active' : ''; ?>" href="delivery.php?tab=active_delivery"><i class="bi bi-truck me-2"></i> My Deliveries</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $tab === 'return_pool' ? 'active' : ''; ?>" href="delivery.php?tab=return_pool"><i class="bi bi-collection me-2"></i> Return Pool</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $tab === 'my_returns' ? 'active' : ''; ?>" href="delivery.php?tab=my_returns"><i class="bi bi-arrow-return-left me-2"></i> My Returns</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $tab === 'history' ? 'active' : ''; ?>" href="delivery.php?tab=history"><i class="bi bi-clock-history me-2"></i> My History</a>
        </li>
    </ul>

    <div class="card card-custom">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Client & Car</th>
                        <th>Dates</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No records found for this view.</td></tr>
                    <?php else: ?>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo $r['customer_name']; ?></div>
                                    <div class="small text-muted mb-2"><?php echo $r['brand'] . ' ' . $r['car_name']; ?> (<?php echo $r['license_plate']; ?>)</div>
                                    <?php if (isset($r['customer_phone'])): ?>
                                        <div class="d-flex gap-2">
                                            <a href="tel:<?php echo $r['customer_phone']; ?>" class="btn btn-xs btn-light text-primary border p-1 rounded"><i class="bi bi-telephone"></i></a>
                                            <a href="https://wa.me/<?php echo $r['customer_phone']; ?>" class="btn btn-xs btn-light text-success border p-1 rounded"><i class="bi bi-whatsapp"></i></a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($tab === 'active_delivery'): ?>
                                        <div class="small fw-bold">Starts: <?php echo $r['start_date']; ?></div>
                                    <?php elseif ($tab === 'my_returns' || $tab === 'return_pool'): ?>
                                        <div class="small fw-bold text-danger">Return: <?php echo $r['end_date']; ?></div>
                                    <?php else: ?>
                                        <div class="small text-muted">Updated: <?php echo $r['updated_at']; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><div class="small" style="max-width:200px;"><?php echo $r['delivery_address'] ?? 'N/A'; ?></div></td>
                                <td>
                                    <?php 
                                        $status = ($tab === 'active_delivery') ? $r['delivery_status'] : $r['return_status'];
                                        $color = $status === 'Out for Delivery' || $status === 'Picked Up' ? 'bg-warning text-dark' : 'bg-info text-white';
                                        if($status === 'Completed' || $status === 'Delivered') $color = 'bg-success text-white';
                                    ?>
                                    <span class="badge-status <?php echo $color; ?>"><?php echo $status; ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($tab === 'active_delivery'): ?>
                                        <form method="POST">
                                            <input type="hidden" name="rentalId" value="<?php echo $r['id']; ?>">
                                            <input type="hidden" name="action" value="delivery_update">
                                            <?php if ($r['delivery_status'] === 'Assigned'): ?>
                                                <input type="hidden" name="status" value="Out for Delivery">
                                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Start Trip</button>
                                            <?php elseif ($r['delivery_status'] === 'Out for Delivery'): ?>
                                                <input type="hidden" name="status" value="Delivered">
                                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">Mark Delivered</button>
                                            <?php endif; ?>
                                        </form>
                                    <?php elseif ($tab === 'return_pool'): ?>
                                        <form method="POST">
                                            <input type="hidden" name="rentalId" value="<?php echo $r['id']; ?>">
                                            <input type="hidden" name="action" value="claim_return">
                                            <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">Claim Return</button>
                                        </form>
                                    <?php elseif ($tab === 'my_returns'): ?>
                                        <form method="POST">
                                            <input type="hidden" name="rentalId" value="<?php echo $r['id']; ?>">
                                            <input type="hidden" name="action" value="return_status_update">
                                            <?php if ($r['return_status'] === 'Requested'): ?>
                                                <input type="hidden" name="status" value="Picked Up">
                                                <button type="submit" class="btn btn-warning btn-sm rounded-pill px-3">Pick Up Car</button>
                                            <?php elseif ($r['return_status'] === 'Picked Up'): ?>
                                                <button type="button" class="btn btn-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#completeReturnModal<?php echo $r['id']; ?>">Complete Back</button>
                                                
                                                <div class="modal fade text-start" id="completeReturnModal<?php echo $r['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content rounded-4 border-0 shadow">
                                                            <div class="modal-header border-0">
                                                                <h5 class="fw-bold">Process Return #<?php echo $r['id']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <?php 
                                                                    $daysLate = max(0, (strtotime('today') - strtotime($r['end_date'])) / 86400);
                                                                    $lateFee = $daysLate * floor($r['total_amount'] / $r['days'] * 1.5); // 1.5x daily rate if late
                                                                ?>
                                                                <div class="mb-3 p-3 bg-light rounded-3">
                                                                    <div class="d-flex justify-content-between small mb-1">
                                                                        <span>Due Date:</span> <span class="fw-bold"><?php echo $r['end_date']; ?></span>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between small">
                                                                        <span>Late Fees (Autocalc):</span> <span class="text-danger fw-bold">$<?php echo number_format($lateFee, 2); ?></span>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label small fw-bold">Manual Adjustment Fees</label>
                                                                    <input type="number" name="return_charges" class="form-control" value="<?php echo $lateFee; ?>" step="0.01">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label small fw-bold">Damage Notes / Condition</label>
                                                                    <textarea name="return_notes" class="form-control" rows="3" placeholder="Describe car condition..."></textarea>
                                                                </div>
                                                                <input type="hidden" name="status" value="Completed">
                                                                <button type="submit" class="btn btn-success w-100 py-3 rounded-3 fw-bold shadow-sm mt-2">Complete Process</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="frontend/script.js"></script>
</body>
</html>
