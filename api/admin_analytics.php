<?php
// api/admin_analytics.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

try {
    // Total Bookings
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM rentals");
    $totalBookings = $stmt->fetch()['total'];

    // Total Revenue
    $stmt = $pdo->query("SELECT SUM(amount) as total FROM payments");
    $totalRevenue = $stmt->fetch()['total'] ?? 0;

    // Most Rented Vehicle
    $stmt = $pdo->query("
        SELECT v.brand, v.name, COUNT(r.id) as count 
        FROM rentals r 
        JOIN vehicles v ON r.vehicle_id = v.id 
        GROUP BY v.id, v.brand, v.name
        ORDER BY count DESC 
        LIMIT 1
    ");
    $mostRented = $stmt->fetch();

    // Monthly Revenue
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as revenue 
        FROM payments 
        GROUP BY month 
        ORDER BY month ASC
    ");
    $monthlyRevenue = [];
    while ($row = $stmt->fetch()) {
        $monthlyRevenue[$row['month']] = (float)$row['revenue'];
    }

    // Maintenance Alerts
    $stmt = $pdo->query("SELECT id, name FROM vehicles WHERE status = 'Maintenance'");
    $maintenanceAlerts = $stmt->fetchAll();

    echo json_encode([
        'total_bookings' => (int)$totalBookings,
        'total_revenue' => (float)$totalRevenue,
        'most_rented_vehicle' => $mostRented ?: null,
        'monthly_revenue' => $monthlyRevenue,
        'maintenance_alerts' => $maintenanceAlerts
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}
?>
