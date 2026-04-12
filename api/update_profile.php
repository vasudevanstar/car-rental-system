<?php
// api/update_profile.php
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
$userId = $_SESSION['user']['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(404);
        echo json_encode(['message' => 'User not found']);
        exit;
    }

    $name = isset($input['name']) ? trim($input['name']) : $user['name'];
    $phone = isset($input['phone']) ? trim($input['phone']) : $user['phone'];
    $password = (isset($input['password']) && strlen($input['password']) >= 6) ? $input['password'] : $user['password'];
    $profilePicture = isset($input['profilePicture']) ? $input['profilePicture'] : $user['profile_picture'];

    $stmt = $pdo->prepare("UPDATE customers SET name = ?, phone = ?, password = ?, profile_picture = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $password, $profilePicture, $userId]);

    // Update Session
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['profilePicture'] = $profilePicture;

    echo json_encode([
        'message' => 'Profile updated successfully',
        'user' => [
            'id' => $userId,
            'name' => $name,
            'email' => $user['email'],
            'role' => $user['role'],
            'profilePicture' => $profilePicture
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error: ' . $e->getMessage()]);
}
?>
