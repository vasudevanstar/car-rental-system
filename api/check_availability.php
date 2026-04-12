<?php
// api/check_availability.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$vehicleId = isset($input['vehicleId']) ? (int)$input['vehicleId'] : 0;
$startDate = isset($input['startDate']) ? $input['startDate'] : '';
$endDate = isset($input['endDate']) ? $input['endDate'] : '';

if (!$vehicleId || !$startDate || !$endDate) {
    echo json_encode(['available' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Check for conflicts
    $stmt = $pdo->prepare("
        SELECT id FROM rentals 
        WHERE vehicle_id = ? 
        AND status NOT IN ('Cancelled', 'Completed')
        AND (start_date <= ? AND end_date >= ?)
    ");
    $stmt->execute([$vehicleId, $endDate, $startDate]);
    
    $isAvailable = $stmt->fetch() ? false : true;

    echo json_encode(['available' => $isAvailable]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error checking availability: ' . $e->getMessage()]);
}
?>
