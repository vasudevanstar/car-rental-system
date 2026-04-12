<?php
// api/profile.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $userId = $_SESSION['user']['id'];
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
            exit;
        }

        echo json_encode([
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'profilePicture' => $user['profile_picture'],
            'loyaltyPoints' => $user['loyalty_points'] ?? 0,
            'favorites' => !empty($user['favorites']) ? json_decode($user['favorites'], true) : [],
            'is_verified' => (bool)$user['is_verified'],
            'driver_license_url' => $user['driver_license_url'] ?? null
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error']);
    }
}
?>
