<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'feeflow_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("ALTER TABLE institutes ADD COLUMN api_key VARCHAR(100) DEFAULT NULL");
    echo "API setup completed successfully.";
}
catch (PDOException $e) {
    if ($e->getCode() == '42S21' || strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "API key column already exists.";
    }
    else {
        echo "Error: " . $e->getMessage();
    }
}
?>
