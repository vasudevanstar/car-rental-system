<?php
// api/book.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$vehicleId = isset($input['vehicleId']) ? (int)$input['vehicleId'] : 0;
$startDate = isset($input['startDate']) ? $input['startDate'] : '';
$endDate = isset($input['endDate']) ? $input['endDate'] : '';
$customerEmail = $_SESSION['user']['email'];

// Verify user exists in DB (Handle cases after DB reset)
$userCheck = $pdo->prepare("SELECT id FROM customers WHERE email = ? AND is_active = 1");
$userCheck->execute([$customerEmail]);
if (!$userCheck->fetch()) {
    session_destroy();
    http_response_code(401);
    echo json_encode(['message' => 'User account no longer exists. Please sign in again.']);
    exit;
}

if (!$vehicleId || !$startDate || !$endDate) {
    http_response_code(400);
    echo json_encode(['message' => 'All booking details are required']);
    exit;
}

try {
    // 1. Get vehicle info
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ? AND is_active = 1");
    $stmt->execute([$vehicleId]);
    $vehicle = $stmt->fetch();
    if (!$vehicle) {
        http_response_code(404);
        echo json_encode(['message' => 'Vehicle not found']);
        exit;
    }

    // 2. Check for conflicts
    $stmt = $pdo->prepare("
        SELECT * FROM rentals 
        WHERE vehicle_id = ? 
        AND status NOT IN ('Cancelled', 'Completed')
        AND (start_date <= ? AND end_date >= ?)
    ");
    $stmt->execute([$vehicleId, $endDate, $startDate]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['message' => 'Selected dates overlap existing booking']);
        exit;
    }

    // 3. Calculate price
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $days = $start->diff($end)->days + 1;
    $total_amount = $vehicle['rent_per_day'] * $days;

    // 4. Create booking
    $stmt = $pdo->prepare("
        INSERT INTO rentals (customer_email, vehicle_id, start_date, end_date, days, total_amount, status)
        VALUES (?, ?, ?, ?, ?, ?, 'Pending')
    ");
    $stmt->execute([$customerEmail, $vehicleId, $startDate, $endDate, $days, $total_amount]);
    $rentalId = $pdo->lastInsertId();

    echo json_encode([
        'message' => 'Booking created and pending confirmation',
        'rental' => [
            'id' => $rentalId,
            'total_amount' => $total_amount,
            'status' => 'Pending'
        ]
    ]);

    // Send confirmation email
    require_once __DIR__ . '/../config/mail.php';
    $vName = $vehicle['brand'] . ' ' . $vehicle['name'];
    $subject = "Booking Confirmation: Your Ride with FastRide";
    $emailBody = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;'>
            <div style='background: #3f66f1; padding: 20px; text-align: center; color: white;'>
                <h2>FastRide Confirmation</h2>
            </div>
            <div style='padding: 20px;'>
                <p>Hello,</p>
                <p>Thank you for choosing FastRide! Your booking is currently <strong>Pending</strong>. Our team will review it shortly.</p>
                <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h3 style='margin-top: 0;'>Booking Details</h3>
                    <p><strong>Vehicle:</strong> {$vName}</p>
                    <p><strong>Period:</strong> {$startDate} to {$endDate}</p>
                    <p><strong>Total Amount:</strong> \${$total_amount}</p>
                    <p><strong>Status:</strong> Pending</p>
                </div>
                <p>You can manage your booking and download your invoice at any time from your history page.</p>
                <a href='http://{$_SERVER['HTTP_HOST']}/history.php' style='display: inline-block; background: #3f66f1; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>View My Rental History</a>
            </div>
            <div style='background: #f1f1f1; padding: 10px; text-align: center; font-size: 12px; color: #888;'>
                &copy; 2026 FastRide Car Rental. All rights reserved.
            </div>
        </div>
    ";
    sendEmail($customerEmail, $subject, $emailBody);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
