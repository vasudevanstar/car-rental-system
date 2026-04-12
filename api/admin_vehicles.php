<?php
// api/admin_vehicles.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC");
        echo json_encode($stmt->fetchAll());
        
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $name = $input['name'] ?? '';
        $brand = $input['brand'] ?? '';
        $type = $input['type'] ?? '';
        $rent = $input['rent_per_day'] ?? 0;
        $status = $input['status'] ?? 'Available';
        $image = $input['image'] ?? '';

        $description = $input['description'] ?? '';
        $transmission = $input['transmission'] ?? 'Automatic';
        $fuel_type = $input['fuel_type'] ?? 'Petrol';
        $seating_capacity = $input['seating_capacity'] ?? 5;
        $model_year = $input['model_year'] ?? date('Y');

        if (!$name || !$type || !$rent) {
            http_response_code(400);
            echo json_encode(['message' => 'Name, Type and Rent are required']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO vehicles (name, brand, type, rent_per_day, status, image, description, transmission, fuel_type, seating_capacity, model_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $brand, $type, $rent, $status, $image, $description, $transmission, $fuel_type, $seating_capacity, $model_year]);
        echo json_encode(['message' => 'Vehicle added successfully', 'id' => $pdo->lastInsertId()]);

    } elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        if (!$id) { http_response_code(400); exit; }

        $name = $input['name'] ?? null;
        $brand = $input['brand'] ?? null;
        $type = $input['type'] ?? null;
        $rent = $input['rent_per_day'] ?? null;
        $status = $input['status'] ?? null;
        $image = $input['image'] ?? null;
        $description = $input['description'] ?? null;
        $transmission = $input['transmission'] ?? null;
        $fuel_type = $input['fuel_type'] ?? null;
        $seating_capacity = $input['seating_capacity'] ?? null;
        $model_year = $input['model_year'] ?? null;

        $fields = []; $params = [];
        if ($name) { $fields[] = "name = ?"; $params[] = $name; }
        if ($brand) { $fields[] = "brand = ?"; $params[] = $brand; }
        if ($type) { $fields[] = "type = ?"; $params[] = $type; }
        if ($rent) { $fields[] = "rent_per_day = ?"; $params[] = $rent; }
        if ($status) { $fields[] = "status = ?"; $params[] = $status; }
        if ($image) { $fields[] = "image = ?"; $params[] = $image; }
        if ($description) { $fields[] = "description = ?"; $params[] = $description; }
        if ($transmission) { $fields[] = "transmission = ?"; $params[] = $transmission; }
        if ($fuel_type) { $fields[] = "fuel_type = ?"; $params[] = $fuel_type; }
        if ($seating_capacity) { $fields[] = "seating_capacity = ?"; $params[] = $seating_capacity; }
        if ($model_year) { $fields[] = "model_year = ?"; $params[] = $model_year; }
        
        if (empty($fields)) { http_response_code(400); exit; }
        
        $params[] = $id;
        $stmt = $pdo->prepare("UPDATE vehicles SET " . implode(', ', $fields) . " WHERE id = ?");
        $stmt->execute($params);
        echo json_encode(['message' => 'Vehicle updated successfully']);

    } elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? 0;
        if (!$id) { http_response_code(400); exit; }
        $stmt = $pdo->prepare("UPDATE vehicles SET is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'Vehicle deleted successfully']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}
?>
