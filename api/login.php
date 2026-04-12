<?php
// api/login.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email = isset($input['email']) ? strtolower(trim($input['email'])) : '';
$password = isset($input['password']) ? $input['password'] : '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['message' => 'Email and password are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ? AND password = ? AND is_active = 1");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid credentials']);
        exit;
    }

    // Prepare user data (Mapping for frontend compatibility)
    $userData = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'profilePicture' => $user['profile_picture'],
        'favorites' => !empty($user['favorites']) ? json_decode($user['favorites'], true) : [],
        'loyaltyPoints' => $user['loyalty_points'] ?? 0
    ];

    // Store in session
    $_SESSION['user'] = $userData;
    
    // For compatibility with script.js, we return a "token" (we'll just use session_id)
    echo json_encode([
        'message' => 'Login successful',
        'token' => session_id(),
        'user' => $userData
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error during login: ' . $e->getMessage()]);
}
?>
