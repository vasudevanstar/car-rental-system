<?php
// api/favorites_get.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

try {
    $userId = $_SESSION['user']['id'];
    $stmt = $pdo->prepare("SELECT favorites FROM customers WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    $favorites = !empty($user['favorites']) ? json_decode($user['favorites'], true) : [];
    
    if (empty($favorites)) {
        echo json_encode([]);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($favorites), '?'));
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id IN ($placeholders)");
    $stmt->execute($favorites);
    $vehicles = $stmt->fetchAll();

    foreach ($vehicles as &$v) {
        if (isset($v['features']) && is_string($v['features'])) {
            $v['features'] = json_decode($v['features'], true);
        }
    }

    echo json_encode($vehicles);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
