<?php
// config/db.php

// Load Composer Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load Environment Variables (for Railway/Local)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
}

// Support for local config.php (for InfinityFree/Shared Hosting)
if (file_exists(__DIR__ . '/config.php')) {
    include __DIR__ . '/config.php';
}

$host = getenv('DB_HOST') ?: ($config['db_host'] ?? 'localhost');
$db   = getenv('DB_NAME') ?: ($config['db_name'] ?? 'car_rental');
$user = getenv('DB_USER') ?: ($config['db_user'] ?? 'root');
$pass = getenv('DB_PASSWORD') ?: ($config['db_pass'] ?? '');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // Enable SSL for secure providers like TiDB Cloud
    PDO::MYSQL_ATTR_SSL_CA       => getenv('DB_SSL_CA') ?: true,
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => (getenv('DB_SSL_VERIFY') !== 'false'),
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
