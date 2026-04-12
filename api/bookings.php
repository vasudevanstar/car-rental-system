<?php
// api/bookings.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

try {
    $email = $_SESSION['user']['email'];
    
    $stmt = $pdo->prepare("
        SELECT r.*, v.name as v_name, v.brand as v_brand, v.image as v_image 
        FROM rentals r
        LEFT JOIN vehicles v ON r.vehicle_id = v.id
        WHERE r.customer_email = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$email]);
    $rentals = $stmt->fetchAll();

    $formatted = array_map(function($r) {
        return [
            'id' => $r['id'],
            'customer_email' => $r['customer_email'],
            'vehicle_id' => $r['vehicle_id'],
            'start_date' => $r['start_date'],
            'end_date' => $r['end_date'],
            'total_amount' => $r['total_amount'],
            'status' => $r['status'],
            'is_rated' => (bool)$r['is_rated'],
            'vehicle' => [
                'id' => $r['vehicle_id'],
                'name' => $r['v_name'],
                'brand' => $r['v_brand'],
                'image' => $r['v_image']
            ]
        ];
    }, $rentals);

    echo json_encode($formatted);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error fetching bookings: ' . $e->getMessage()]);
}
?>
