<?php
// api/favorites_toggle.php
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

if (!$vehicleId) {
    http_response_code(400);
    echo json_encode(['message' => 'vehicleId is required']);
    exit;
}

try {
    $userId = $_SESSION['user']['id'];
    $stmt = $pdo->prepare("SELECT favorites FROM customers WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    $favorites = !empty($user['favorites']) ? json_decode($user['favorites'], true) : [];
    
    $index = array_search($vehicleId, $favorites);
    if ($index !== false) {
        array_splice($favorites, $index, 1);
    } else {
        $favorites[] = $vehicleId;
    }

    $stmt = $pdo->prepare("UPDATE customers SET favorites = ? WHERE id = ?");
    $stmt->execute([json_encode($favorites), $userId]);

    // Update session
    $_SESSION['user']['favorites'] = $favorites;

    echo json_encode(['message' => 'Favorites updated', 'favorites' => $favorites]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
