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

    // 4. Update vehicle status
    $stmt = $pdo->prepare("UPDATE vehicles SET status = 'Booked' WHERE id = ?");
    $stmt->execute([$rental['vehicle_id']]);

    echo json_encode(['message' => 'Payment processed and rental confirmed']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
