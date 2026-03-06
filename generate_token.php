<?php
require_once 'includes/config.php';
if (!is_logged_in())
    redirect('index.php');

$student_id = $_GET['id'] ?? null;
if (!$student_id)
    die("Invalid Request");

$token = bin2hex(random_bytes(16));

$stmt = $pdo->prepare("UPDATE students SET ledger_token = ? WHERE id = ? AND institute_id = ?");
$stmt->execute([$token, $student_id, get_institute_id()]);

redirect("student_ledger.php?id=$student_id");
?>
