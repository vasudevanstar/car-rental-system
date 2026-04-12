<?php
// api/blocked_dates.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$vehicleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$vehicleId) {
    echo json_encode([]);
    exit;
}

try {
    // Get all non-cancelled/non-completed rentals for this vehicle
    $stmt = $pdo->prepare("
        SELECT start_date as `from`, end_date as `to` 
        FROM rentals 
        WHERE vehicle_id = ? 
        AND status NOT IN ('Cancelled', 'Completed')
    ");
    $stmt->execute([$vehicleId]);
    $blocked = $stmt->fetchAll();

    echo json_encode($blocked);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error fetching blocked dates: ' . $e->getMessage()]);
}
?>
