<?php
// api/upload.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$type = isset($_GET['type']) ? $_GET['type'] : 'profile'; // 'profile' or 'license'
$field = ($type === 'license') ? 'licenseImage' : 'image';

if (!isset($_FILES[$field])) {
    http_response_code(400);
    echo json_encode(['message' => 'No file uploaded']);
    exit;
}

$file = $_FILES[$field];
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = time() . '-' . uniqid() . '.' . $ext;
$targetPath = UPLOAD_DIR . $filename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    $url = 'uploads/' . $filename;
    
    if ($type === 'license') {
        try {
            $stmt = $pdo->prepare("UPDATE customers SET driver_license_url = ?, is_verified = 0 WHERE id = ?");
            $stmt->execute([$url, $_SESSION['user']['id']]);
        } catch (PDOException $e) {
            // Log error
        }
    }

    echo json_encode([
        'message' => 'Upload successful',
        'url' => $url
    ]);
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to move uploaded file']);
}
?>
