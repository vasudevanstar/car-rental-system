<?php
// api/admin_bookings.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("
            SELECT r.*, 
                   v.brand as vehicle_brand, v.name as vehicle_name, v.type as vehicle_type, v.image as vehicle_image,
                   c.name as customer_name, c.phone as customer_phone
            FROM rentals r 
            LEFT JOIN vehicles v ON r.vehicle_id = v.id 
            LEFT JOIN customers c ON r.customer_email = c.email 
            ORDER BY r.id DESC
        ");
        echo json_encode($stmt->fetchAll());
    } elseif ($method === 'PATCH' || $method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $status = $input['status'] ?? '';

        if (!$id || !$status) {
            http_response_code(400);
            exit;
        }

        // Fetch vehicle_id before update
        $checkStmt = $pdo->prepare("SELECT vehicle_id FROM rentals WHERE id = ?");
        $checkStmt->execute([$id]);
        $rental = $checkStmt->fetch();

        if ($rental) {
            $pdo->beginTransaction();
            try {
                // Update rental status
                $stmt = $pdo->prepare("UPDATE rentals SET status = ? WHERE id = ?");
                $stmt->execute([$status, $id]);

                // Logic: If Completed or Cancelled, vehicle becomes Available.
                // Otherwise, it stays/becomes Booked.
                $newVehicleStatus = ($status === 'Completed' || $status === 'Cancelled') ? 'Available' : 'Booked';
                
                $vStmt = $pdo->prepare("UPDATE vehicles SET status = ? WHERE id = ?");
                $vStmt->execute([$newVehicleStatus, $rental['vehicle_id']]);

                $pdo->commit();
                echo json_encode(['message' => 'Status updated and vehicle availability synced.']);
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Booking not found']);
        }
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}
?>
