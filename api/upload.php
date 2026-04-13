<?php
// api/upload.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

// Determine upload type based on route or field name
$requestUri = $_SERVER['REQUEST_URI'];
$isLicense = (strpos($requestUri, 'upload-license') !== false) || isset($_FILES['licenseImage']);
$field = $isLicense ? 'licenseImage' : 'image';

if (!isset($_FILES[$field])) {
    http_response_code(400);
    echo json_encode(['message' => 'No file uploaded under expected field: ' . $field]);
    exit;
}

$file = $_FILES[$field];
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = time() . '-' . uniqid() . '.' . $ext;
$targetPath = UPLOAD_DIR . $filename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    $url = 'uploads/' . $filename;
    
    if ($isLicense) {
        try {
            $stmt = $pdo->prepare("UPDATE customers SET driver_license_url = ?, is_verified = 0 WHERE id = ?");
            $stmt->execute([$url, $_SESSION['user']['id']]);
            // Update session data to reflect change
            $_SESSION['user']['driver_license_url'] = $url;
            $_SESSION['user']['is_verified'] = 0;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['message' => 'DB Error: ' . $e->getMessage()]);
            exit;
        }
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE customers SET profile_picture = ? WHERE id = ?");
            $stmt->execute([$url, $_SESSION['user']['id']]);
            $_SESSION['user']['profile_picture'] = $url;
        } catch (PDOException $e) {
            // Silently fail or log
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
