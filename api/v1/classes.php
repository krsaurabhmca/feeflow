<?php
require_once 'api_helper.php';
$institute = authenticate();
$institute_id = $institute['id'];

$stmt = $pdo->prepare("SELECT * FROM classes WHERE institute_id = ? ORDER BY class_name ASC");
$stmt->execute([$institute_id]);
$classes = $stmt->fetchAll();

sendResponse(true, "Classes list", $classes);
?>
