<?php
// api/admin_verifications.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT id, name, email, driver_license_url FROM customers WHERE driver_license_url IS NOT NULL AND is_verified = 0");
        echo json_encode($stmt->fetchAll());
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($input['id']) ? (int)$input['id'] : 0;
        $verified = isset($input['verified']) ? (bool)$input['verified'] : false;

        if (!$id) {
            http_response_code(400);
            exit;
        }

        if ($verified) {
            $stmt = $pdo->prepare("UPDATE customers SET is_verified = 1 WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE customers SET driver_license_url = NULL, is_verified = 0 WHERE id = ?");
        }
        $stmt->execute([$id]);

        // 5. Log Activity
        try {
            $logStmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, details) VALUES (?, ?, ?)");
            $action = $verified ? 'User Verified' : 'Verification Rejected';
            $logStmt->execute([$_SESSION['user']['id'], $action, "Processed verification for Customer ID #{$id}"]);
        } catch (Exception $e) { }

        echo json_encode(['message' => $verified ? 'User verified' : 'Verification rejected']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}
?>
