<?php
// migrate_db.php
require_once __DIR__ . '/config/db.php';

try {
    echo "Connecting to database: $db...\n";
    
    $sql = file_get_contents(__DIR__ . '/init_db.sql');
    if ($sql === false) {
        throw new Exception("Could not read init_db.sql");
    }
    
    // Execute the SQL
    $pdo->exec($sql);
    echo "Database migrated successfully to Aiven!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
