<?php
require_once 'api_helper.php';
$tables = ['students', 'fees', 'institutes', 'classes', 'fee_categories'];
$results = [];

foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("DESCRIBE $t");
        $results[$t] = $stmt->fetchAll();
    }
    catch (Exception $e) {
        $results[$t] = "Error: " . $e->getMessage();
    }
}

sendResponse(true, "Schema check", $results);
?>
