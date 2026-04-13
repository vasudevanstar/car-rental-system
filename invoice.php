<?php
// invoice.php
require_once __DIR__ . '/config/db.php';

if (!isset($_GET['id'])) {
    die("Booking ID required.");
}

$rentalId = (int)$_GET['id'];

try {
    // Fetch rental details
    $stmt = $pdo->prepare("
        SELECT r.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
               v.brand, v.name as car_name, v.license_plate, v.color, v.rent_per_day
        FROM rentals r
        JOIN customers c ON r.customer_email = c.email
        JOIN vehicles v ON r.vehicle_id = v.id
        WHERE r.id = ?
    ");
    $stmt->execute([$rentalId]);
    $rental = $stmt->fetch();

    if (!$rental) {
        die("Booking not found.");
    }

    // Fetch payment details
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE rental_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$rentalId]);
    $payment = $stmt->fetch();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $rentalId; ?> - FastRide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; }
        .invoice-card {
            background: #fff;
            max-width: 850px;
            margin: 50px auto;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #eee;
        }
        .invoice-header {
            background: linear-gradient(135deg, #3f66f1 0%, #6366f1 100%);
            padding: 40px;
            color: white;
        }
        .invoice-body { padding: 50px; }
        .table-custom th { background: #f8f9fa; border-top: none; }
        .total-section { background: #f8f9fa; border-radius: 12px; padding: 20px; }
        @media print {
            body { background: white; }
            .invoice-card { margin: 0; box-shadow: none; border: none; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

<div class="container container-print">
    <div class="invoice-card">
        <div class="invoice-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-1 fw-bold"><i class="bi bi-car-front-fill me-2"></i> FASTRIDE</h1>
                <p class="mb-0 opacity-75">Premium Car Rental Services</p>
            </div>
            <div class="text-end">
                <h2 class="mb-0">INVOICE</h2>
                <p class="mb-0 opacity-75">#FR-<?php echo str_pad($rentalId, 5, '0', STR_PAD_LEFT); ?></p>
            </div>
        </div>
        
        <div class="invoice-body">
            <div class="row mb-5">
                <div class="col-6">
                    <h6 class="text-muted text-uppercase fw-bold mb-3">Billed To:</h6>
                    <h5 class="fw-bold mb-1"><?php echo $rental['customer_name']; ?></h5>
                    <p class="text-muted mb-1"><?php echo $rental['customer_email']; ?></p>
                    <p class="text-muted mb-0"><?php echo $rental['customer_phone']; ?></p>
                </div>
                <div class="col-6 text-end">
                    <h6 class="text-muted text-uppercase fw-bold mb-3">Rental Details:</h6>
                    <p class="mb-1"><strong>Date:</strong> <?php echo date('d M Y', strtotime($rental['created_at'])); ?></p>
                    <p class="mb-1"><strong>Period:</strong> <?php echo $rental['start_date']; ?> to <?php echo $rental['end_date']; ?></p>
                    <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success">PAID</span></p>
                </div>
            </div>

            <table class="table table-custom mb-5">
                <thead>
                    <tr>
                        <th class="ps-0">Description</th>
                        <th class="text-center">Days</th>
                        <th class="text-end">Rate/Day</th>
                        <th class="text-end pe-0">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-0">
                            <h6 class="fw-bold mb-1"><?php echo $rental['brand'] . ' ' . $rental['car_name']; ?></h6>
                            <small class="text-muted">Plate: <?php echo $rental['license_plate']; ?> | Color: <?php echo $rental['color']; ?></small>
                        </td>
                        <td class="text-center"><?php echo $rental['days']; ?></td>
                        <td class="text-end">$<?php echo number_format($rental['rent_per_day'], 2); ?></td>
                        <td class="text-end pe-0 fw-bold">$<?php echo number_format($rental['total_amount'], 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="row">
                <div class="col-7">
                    <div class="p-3 border rounded-3 bg-light opacity-75 small">
                        <strong>Terms & Conditions:</strong><br>
                        - Please bring your original driving license at the time of pickup.<br>
                        - Vehicles should be returned with a full tank of fuel.<br>
                        - Any damage occurred during the rental period is subject to insurance claims.
                    </div>
                </div>
                <div class="col-5">
                    <div class="total-section">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span>$<?php echo number_format($rental['total_amount'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tax (0%):</span>
                            <span>$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5 class="fw-bold mb-0">Total Paid:</h5>
                            <h5 class="fw-bold mb-0 text-primary">$<?php echo number_format($rental['total_amount'], 2); ?></h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 no-print">
                <button onclick="window.print()" class="btn btn-primary btn-lg px-5 rounded-pill shadow btn-print">
                    <i class="bi bi-printer me-2"></i> Print or Save as PDF
                </button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
