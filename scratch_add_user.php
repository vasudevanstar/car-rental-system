<?php
require 'config/db.php';
$email = 'vasudevan12506@gmail.com';
$pass = 'pass12345';
$name = 'Vasudevan';

try {
    $stmt = $pdo->prepare("INSERT INTO customers (name, email, password, role, is_verified) 
                           VALUES (?, ?, ?, 'admin', 1) 
                           ON DUPLICATE KEY UPDATE role = 'admin', password = ?");
    $stmt->execute([$name, $email, $pass, $pass]);
    echo "User $email set as admin successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
