<?php
require_once 'includes/config.php';
if (!is_logged_in())
    redirect('index.php');

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM fee_categories WHERE id = ? AND institute_id = ?");
    $stmt->execute([$id, get_institute_id()]);
}
redirect('fee_categories.php');
