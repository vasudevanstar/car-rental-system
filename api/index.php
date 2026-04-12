<?php
// api/index.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

// Get the requested route from the URL
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/api'; // Standard base path

// Strip base path and query strings
$route = str_replace($basePath, '', $requestUri);
$route = explode('?', $route)[0]; // Remove query string
$route = rtrim($route, '/');

// Parse dynamic routes (e.g., /vehicles/123)
if (preg_match('/^\/vehicles\/(\d+)$/', $route, $matches)) {
    $_GET['id'] = $matches[1];
    $route = '/vehicles/id';
}
if (preg_match('/^\/vehicles\/(\d+)\/blocked-dates$/', $route, $matches)) {
    $_GET['id'] = $matches[1];
    $route = '/vehicles/blocked-dates';
}

// Routing logic (Internal matches without /api prefix)
switch ($route) {
    case '/vehicles/blocked-dates':
        require 'blocked_dates.php';
        break;
    case '/vehicles/id':
    case '/vehicles':
        require 'vehicles.php';
        break;
    case '/login':
        require 'login.php';
        break;
    case '/register':
        require 'register.php';
        break;
    case '/favorites/toggle':
        require 'favorites_toggle.php';
        break;
    case '/favorites':
        require 'favorites_get.php';
        break;
    case '/profile':
        require 'profile.php';
        break;
    case '/update-profile':
        require 'update_profile.php';
        break;
    case '/upload-profile-picture':
    case '/upload-license':
        require 'upload.php';
        break;
    case '/reviews':
        require 'reviews.php';
        break;
    case '/rate-vehicle':
        require 'reviews.php';
        break;
    case '/bookings':
        require 'bookings.php';
        break;
    case '/book':
        require 'book.php';
        break;
    case '/payment':
        require 'payment.php';
        break;
    case '/cancel-booking':
        require 'cancel_booking.php';
        break;
    case '/promotions':
        require 'admin_promotions.php';
        break;
    case '/admin/analytics':
        require 'admin_analytics.php';
        break;
    case '/admin/vehicles':
    case '/admin/add-vehicle':
    case '/admin/update-vehicle':
    case '/admin/delete-vehicle':
        require 'admin_vehicles.php';
        break;
    case '/admin/bookings':
        require 'admin_bookings.php';
        break;
    case '/admin/verifications':
    case '/admin/verify':
        require 'admin_verifications.php';
        break;
    case '/admin/users':
        require 'admin_users.php';
        break;
    case '/check-availability':
        require 'check_availability.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Route not found: ' . $route]);
        break;
}
?>
