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

    // --- NEW ENHANCEMENTS ---

    // 1. Utilization Rate (Booked / Total Active)
    $stmt = $pdo->query("SELECT (SELECT COUNT(*) FROM vehicles WHERE status = 'Booked') as booked, (SELECT COUNT(*) FROM vehicles WHERE is_active = 1) as total");
    $utilizationData = $stmt->fetch();
    $utilizationRate = $utilizationData['total'] > 0 ? ($utilizationData['booked'] / $utilizationData['total'] * 100) : 0;

    // 2. Revenue by Vehicle Type
    $stmt = $pdo->query("
        SELECT v.type, SUM(p.amount) as revenue 
        FROM payments p 
        JOIN rentals r ON p.rental_id = r.id 
        JOIN vehicles v ON r.vehicle_id = v.id 
        GROUP BY v.type
    ");
    $revenueByType = [];
    while($row = $stmt->fetch()) {
        $revenueByType[$row['type']] = (float)$row['revenue'];
    }

    // 3. Recent Activity Logs
    $stmt = $pdo->query("
        SELECT a.*, c.name as user_name, c.profile_picture 
        FROM activity_log a 
        LEFT JOIN customers c ON a.user_id = c.id 
        ORDER BY a.created_at DESC 
        LIMIT 10
    ");
    $recentActivity = $stmt->fetchAll();

    echo json_encode([
        'total_bookings' => (int)$totalBookings,
        'total_revenue' => (float)$totalRevenue,
        'most_rented_vehicle' => $mostRented ?: null,
        'monthly_revenue' => $monthlyRevenue,
        'maintenance_alerts' => $maintenanceAlerts,
        'utilization_rate' => round($utilizationRate, 1),
        'revenue_by_type' => $revenueByType,
        'recent_activity' => $recentActivity
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}
?>
