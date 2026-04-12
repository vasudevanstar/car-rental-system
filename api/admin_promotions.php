<?php
// api/admin_promotions.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM promotions WHERE is_active = 1 LIMIT 1");
        echo json_encode($stmt->fetch());
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $title = $input['title'] ?? '';
        $desc = $input['description'] ?? '';
        $code = $input['promo_code'] ?? '';
        $discount = $input['discount_percentage'] ?? 0;

        if (!$title || !$code || !$discount) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing promotion details']);
            exit;
        }

        // Deactivate old promos
        $pdo->query("UPDATE promotions SET is_active = 0");

        $stmt = $pdo->prepare("INSERT INTO promotions (title, description, promo_code, discount_percentage, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$title, $desc, $code, $discount]);
        
        echo json_encode(['message' => 'Promotion broadcasted successfully']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}
?>
