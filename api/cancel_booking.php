<?php
// api/cancel_booking.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$bookingId = isset($input['rentalId']) ? (int)$input['rentalId'] : 0;
$email = $_SESSION['user']['email'];

if (!$bookingId) {
    http_response_code(400);
    echo json_encode(['message' => 'Booking ID is required']);
    exit;
}

try {
    // 1. Verify ownership and current status (Fetch vehicle_id as well)
    $stmt = $pdo->prepare("SELECT id, status, vehicle_id FROM rentals WHERE id = ? AND customer_email = ?");
    $stmt->execute([$bookingId, $email]);
    $booking = $stmt->fetch();

    if (!$booking) {
        http_response_code(404);
        echo json_encode(['message' => 'Booking not found or access denied']);
        exit;
    }

    if ($booking['status'] === 'Cancelled' || $booking['status'] === 'Completed') {
        http_response_code(400);
        echo json_encode(['message' => 'Booking cannot be cancelled in its current state: ' . $booking['status']]);
        exit;
    }

    // 2. Wrap in a transaction for data integrity
    $pdo->beginTransaction();
    try {
        // Update rental status
        $stmt = $pdo->prepare("UPDATE rentals SET status = 'Cancelled' WHERE id = ?");
        $stmt->execute([$bookingId]);

        // Reset vehicle status back to 'Available'
        $stmt = $pdo->prepare("UPDATE vehicles SET status = 'Available' WHERE id = ?");
        $stmt->execute([$booking['vehicle_id']]);

        $pdo->commit();
        echo json_encode(['message' => 'Booking cancelled successfully and vehicle released.']);
    } catch (Exception $ex) {
        $pdo->rollBack();
        throw $ex;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error cancelling booking: ' . $e->getMessage()]);
}
?>
