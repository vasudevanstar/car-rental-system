<?php
// config/db.php

$host = 'localhost';
$db   = 'car_rental';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die("Connection failed: " . $e->getMessage());
}

// Start session for all pages
if (session_status() === PHP_SESSION_NONE) {
    // Configuration
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Mailer Configuration (Defaults)
define('MAIL_FROM', 'FastRide@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
define('MAIL_NAME', 'FastRide Cars');

session_start();

}
?>
