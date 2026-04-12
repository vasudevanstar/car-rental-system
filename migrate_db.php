<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'car_rental';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents('init_db.sql');
    $pdo->exec($sql);
    echo "Database migrated successfully.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
