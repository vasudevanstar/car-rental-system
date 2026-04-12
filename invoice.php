<?php
// invoice.php
require_once __DIR__ . '/config/db.php';

// Check if rentalId is provided
if (!isset($_GET['rentalId'])) {
    die("Error: No rental ID provided.");
}

$rentalId = (int)$_GET['rentalId'];

// Fetch rental, vehicle, and customer details
$stmt = $pdo->prepare("
    SELECT r.*, v.name as vehicle_name, v.brand as vehicle_brand, c.name as customer_name, c.phone as customer_phone
    FROM rentals r
    JOIN vehicles v ON r.vehicle_id = v.id
    JOIN customers c ON r.customer_email = c.email
    WHERE r.id = ?
");
$stmt->execute([$rentalId]);
$rental = $stmt->fetch();

if (!$rental) {
    die("Error: Rental record not found.");
}

// Check if generating PDF or viewing normally
$isPdf = defined('IS_GENERATING_PDF');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $rental['id']; ?> - FastRide</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); font-size: 16px; background: #fff; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #6366f1; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #6366f1; margin: 0; font-size: 28px; text-transform: uppercase; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-block h3 { margin-top: 0; font-size: 14px; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; }
        .info-block p { margin: 0; font-size: 15px; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; margin-bottom: 30px; }
        table th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 12px; font-weight: bold; text-transform: uppercase; font-size: 13px; }
        table td { padding: 12px; border-bottom: 1px solid #eee; }
        .total-section { text-align: right; padding-top: 20px; }
        .total-amount { font-size: 24px; font-weight: bold; color: #6366f1; }
        .footer-note { margin-top: 50px; text-align: center; color: #94a3b8; font-size: 12px; }
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; color: #fff; }
        .status-Completed { background: #10b981; }
        .status-Pending { background: #f59e0b; }
        .status-Confirmed { background: #3b82f6; }
        
        <?php if ($isPdf): ?>
        /* PDF specific overrides */
        .invoice-box { box-shadow: none; border: none; padding: 0; }
        body { padding: 0; }
        <?php endif; ?>
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>FastRide</h1>
                <p>Premium Car Rental Service</p>
            </div>
            <div style="text-align: right;">
                <p><strong>INVOICE</strong></p>
                <p>#<?php echo $rental['id']; ?></p>
                <p>Date: <?php echo date('d M Y'); ?></p>
            </div>
        </div>

        <div class="info-section">
            <div class="info-block" style="width: 48%;">
                <h3>Billed To:</h3>
                <p><strong><?php echo $rental['customer_name']; ?></strong></p>
                <p><?php echo $rental['customer_email']; ?></p>
                <p><?php echo $rental['customer_phone']; ?></p>
            </div>
            <div style="width: 48%;">
                <h3>Booking Details:</h3>
                <p><strong>Status:</strong> <span class="status-badge status-<?php echo $rental['status']; ?>"><?php echo $rental['status']; ?></span></p>
                <p><strong>Period:</strong> <?php echo $rental['start_date']; ?> to <?php echo $rental['end_date']; ?></p>
                <p><strong>ID:</strong> RENT-<?php echo str_pad($rental['id'], 5, '0', STR_PAD_LEFT); ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Days</th>
                    <th style="text-align: right;">Rate / Day</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?php echo $rental['vehicle_brand'] . ' ' . $rental['vehicle_name']; ?></strong><br>
                        <small>Premium Rental Vehicle</small>
                    </td>
                    <td style="text-align: center;"><?php echo $rental['days']; ?></td>
                    <td style="text-align: right;">$<?php echo number_format($rental['total_amount'] / ($rental['days'] ?: 1), 2); ?></td>
                    <td style="text-align: right;">$<?php echo number_format($rental['total_amount'], 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <p style="margin-bottom: 5px; color: #64748b;">Grand Total</p>
            <div class="total-amount">$<?php echo number_format($rental['total_amount'], 2); ?></div>
        </div>

        <div class="footer-note">
            <p>Thank you for choosing FastRide! Drive safely.</p>
            <p>This is a computer-generated invoice and doesn't require a physical signature.</p>
        </div>
    </div>
</body>
</html>
