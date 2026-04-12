<?php
// api/register.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$name = isset($input['name']) ? trim($input['name']) : '';
$email = isset($input['email']) ? strtolower(trim($input['email'])) : '';
$password = isset($input['password']) ? $input['password'] : '';
$phone = isset($input['phone']) ? trim($input['phone']) : '';

if (!$name || !$email || !$password || !$phone) {
    http_response_code(400);
    echo json_encode(['message' => 'All fields are required']);
    exit;
}

try {
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['message' => 'Email already registered']);
        exit;
    }

    // Insert new user
    $role = 'customer';
    $is_active = 1;
    $stmt = $pdo->prepare("INSERT INTO customers (name, email, password, phone, role, is_active) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $phone, $role, $is_active]);
    
    $userId = $pdo->lastInsertId();

    echo json_encode([
        'message' => 'Registration successful',
        'customer' => [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'role' => $role
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error during registration: ' . $e->getMessage()]);
}
?>
