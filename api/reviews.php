<?php
// api/reviews.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $vehicleId = isset($_GET['vehicleId']) ? (int)$_GET['vehicleId'] : 0;
    if (!$vehicleId) {
        http_response_code(400);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT r.*, c.name as customer_name, c.profile_picture as customer_image 
            FROM reviews r
            JOIN customers c ON r.customer_id = c.id
            WHERE r.vehicle_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$vehicleId]);
        echo json_encode($stmt->fetchAll());
    } catch (PDOException $e) {
        http_response_code(500);
    }
} elseif ($method === 'POST') {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $vehicleId = isset($input['vehicleId']) ? (int)$input['vehicleId'] : 0;
    $rentalId = isset($input['rentalId']) ? (int)$input['rentalId'] : 0;
    $rating = isset($input['rating']) ? (int)$input['rating'] : 0;
    $comment = isset($input['review']) ? trim($input['review']) : '';

    if (!$vehicleId || !$rentalId || !$rating) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing rating details']);
        exit;
    }

    try {
        // Simple verification
        $stmt = $pdo->prepare("SELECT * FROM rentals WHERE id = ? AND customer_email = ?");
        $stmt->execute([$rentalId, $_SESSION['user']['email']]);
        if (!$stmt->fetch()) {
            http_response_code(403);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO reviews (vehicle_id, customer_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$vehicleId, $_SESSION['user']['id'], $rating, $comment]);

        $stmt = $pdo->prepare("UPDATE rentals SET is_rated = 1 WHERE id = ?");
        $stmt->execute([$rentalId]);

        // Update vehicle avg rating
        $stmt = $pdo->prepare("SELECT AVG(rating) as avgRating FROM reviews WHERE vehicle_id = ?");
        $stmt->execute([$vehicleId]);
        $newAvg = round($stmt->fetch()['avgRating'], 1);
        
        $stmt = $pdo->prepare("UPDATE vehicles SET rating_average = ? WHERE id = ?");
        $stmt->execute([$newAvg, $vehicleId]);

        echo json_encode(['message' => 'Review submitted successfully', 'rating_average' => $newAvg]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => $e->getMessage()]);
    }
}
?>
