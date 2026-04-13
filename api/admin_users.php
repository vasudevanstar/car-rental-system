<?php
// api/admin_users.php
require_once __DIR__ . '/../config/db.php';

// Protect the endpoint
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['message' => 'Forbidden']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Fetch all users
        $stmt = $pdo->query("SELECT id, name, email, phone, role, is_verified, is_active, created_at FROM customers ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    } 
    elseif ($method === 'POST') {
        // Update user status or role (Toggle isActive)
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['userId'])) {
            throw new Error('User ID is required');
        }

        $userId = $data['userId'];
        $action = $data['action'] ?? 'toggle_active';

        if ($action === 'toggle_active') {
            $stmt = $pdo->prepare("UPDATE customers SET is_active = NOT is_active WHERE id = ?");
            $stmt->execute([$userId]);
            echo json_encode(['message' => 'User status updated successfully']);
        } elseif ($action === 'change_role') {
            $newRole = $data['role'];
            $stmt = $pdo->prepare("UPDATE customers SET role = ? WHERE id = ?");
            $stmt->execute([$newRole, $userId]);
            echo json_encode(['message' => 'User role updated to ' . $newRole]);
        } else {
            throw new Error('Invalid action');
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}
?>
