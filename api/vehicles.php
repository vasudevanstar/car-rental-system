<?php
// api/vehicles.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

try {
    // Single Vehicle Fetch
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ? AND is_active = 1");
        $stmt->execute([$_GET['id']]);
        $vehicle = $stmt->fetch();
        
        if (!$vehicle) {
            http_response_code(404);
            echo json_encode(['message' => 'Vehicle not found']);
            exit;
        }

        // Include reviews (using standardized snake_case columns)
        $revStmt = $pdo->prepare("SELECT r.*, c.name FROM reviews r JOIN customers c ON r.customer_id = c.id WHERE r.vehicle_id = ? ORDER BY r.created_at DESC");
        $revStmt->execute([$_GET['id']]);
        $vehicle['reviews'] = $revStmt->fetchAll();

        echo json_encode($vehicle);
        exit;
    }

    $query = "SELECT * FROM vehicles WHERE is_active = 1";
    $params = [];

    if (isset($_GET['type']) && $_GET['type'] !== '') {
        $query .= " AND type = ?";
        $params[] = $_GET['type'];
    }
    if (isset($_GET['status']) && $_GET['status'] !== '') {
        $query .= " AND status = ?";
        $params[] = $_GET['status'];
    }
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        $query .= " AND (name LIKE ? OR brand LIKE ?)";
        $params[] = "%" . $_GET['search'] . "%";
        $params[] = "%" . $_GET['search'] . "%";
    }
    if (isset($_GET['minPrice']) && $_GET['minPrice'] !== '') {
        $query .= " AND rent_per_day >= ?";
        $params[] = (float)$_GET['minPrice'];
    }
    if (isset($_GET['maxPrice']) && $_GET['maxPrice'] !== '') {
        $query .= " AND rent_per_day <= ?";
        $params[] = (float)$_GET['maxPrice'];
    }
    if (isset($_GET['brand']) && $_GET['brand'] !== '') {
        $query .= " AND brand = ?";
        $params[] = $_GET['brand'];
    }
    if (isset($_GET['transmission']) && $_GET['transmission'] !== '') {
        $query .= " AND transmission = ?";
        $params[] = $_GET['transmission'];
    }
    if (isset($_GET['fuel_type']) && $_GET['fuel_type'] !== '') {
        $query .= " AND fuel_type = ?";
        $params[] = $_GET['fuel_type'];
    }
    if (isset($_GET['seats']) && $_GET['seats'] !== '') {
        $query .= " AND seating_capacity >= ?";
        $params[] = (int)$_GET['seats'];
    }

    $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
    if ($sort === 'price_asc') $query .= " ORDER BY rent_per_day ASC";
    elseif ($sort === 'price_desc') $query .= " ORDER BY rent_per_day DESC";
    elseif ($sort === 'rating_desc') $query .= " ORDER BY rating_average DESC";
    else $query .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $allVehicles = $stmt->fetchAll();

    // Parse JSON fields (features, ratings) if necessary
    foreach ($allVehicles as &$v) {
        if (isset($v['features']) && is_string($v['features'])) {
            $v['features'] = json_decode($v['features'], true);
        }
        if (isset($v['ratings']) && is_string($v['ratings'])) {
            $v['ratings'] = json_decode($v['ratings'], true);
        }
    }

    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 9;
    $start = ($page - 1) * $perPage;
    
    $total = count($allVehicles);
    $paginated = array_slice($allVehicles, $start, $perPage);

    echo json_encode([
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'vehicles' => $paginated
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error fetching vehicles: ' . $e->getMessage()]);
}
?>
