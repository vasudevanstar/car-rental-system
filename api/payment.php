<?php
// api/payment.php
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
$rentalId = isset($input['rentalId']) ? (int)$input['rentalId'] : 0;
$amount = isset($input['amount']) ? (float)$input['amount'] : 0;
$method = isset($input['method']) ? $input['method'] : '';

if (!$rentalId || !$amount || !$method) {
    http_response_code(400);
    echo json_encode(['message' => 'Payment details are required']);
    exit;
}

try {
    // 1. Check rental
    $stmt = $pdo->prepare("SELECT * FROM rentals WHERE id = ?");
    $stmt->execute([$rentalId]);
    $rental = $stmt->fetch();
    if (!$rental) {
        http_response_code(404);
        echo json_encode(['message' => 'Rental not found']);
        exit;
    }

    // 2. Record payment
    $stmt = $pdo->prepare("INSERT INTO payments (rental_id, amount, method) VALUES (?, ?, ?)");
    $stmt->execute([$rentalId, $amount, $method]);

    // 3. Update rental status
    $stmt = $pdo->prepare("UPDATE rentals SET status = 'Confirmed' WHERE id = ?");
    $stmt->execute([$rentalId]);

    // 4. Update vehicle status & Fuel Level (Simulated)
    $stmt = $pdo->prepare("UPDATE vehicles SET status = 'Booked', fuel_level = 100 WHERE id = ?");
    $stmt->execute([$rental['vehicle_id']]);

    // 5. Log Activity
    try {
        $logStmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, details) VALUES ((SELECT id FROM customers WHERE email = ?), 'Payment Received', 'Rental #' . ? . ' paid with ' . ? . '. Amount: $' . ?)");
        $logStmt->execute([$rental['customer_email'], $rentalId, $method, $amount]);
    } catch (Exception $e) { /* Non-critical error */ }

    // 6. Send Confirmation Email with Invoice Link
    try {
        require_once __DIR__ . '/../config/mail.php';
        $subject = "Payment Confirmed: Your Booking #$rentalId is Ready!";
        $invoiceUrl = "http://" . $_SERVER['HTTP_HOST'] . "/invoice.php?id=" . $rentalId;
        
        $emailBody = "
            <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #eee; border-radius: 10px; padding: 30px;'>
                <h2 style='color: #3f66f1; text-align: center;'>Payment Received!</h2>
                <p>Hello,</p>
                <p>Great news! We have received your payment of <strong>\$$amount</strong> via <strong>$method</strong>. Your vehicle is now officially confirmed for your trip.</p>
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <p style='margin: 0;'><strong>Booking ID:</strong> #$rentalId</p>
                    <p style='margin: 10px 0 0 0;'><strong>Status:</strong> Confirmed & Ready for Pickup/Delivery</p>
                </div>
                <p>You can view and download your official invoice using the button below:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$invoiceUrl' style='background: #3f66f1; color: white; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>View & Download Invoice</a>
                </div>
                <hr style='border: 0; border-top: 1px solid #eee;'>
                <p style='font-size: 12px; color: #888; text-align: center;'>FastRide Car Rental &bull; Premium Fleet Services</p>
            </div>
        ";
        sendEmail($rental['customer_email'], $subject, $emailBody);
    } catch (Exception $e) { /* Mail failure shouldn't stop the payment success response */ }

    echo json_encode(['message' => 'Payment processed and rental confirmed']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
