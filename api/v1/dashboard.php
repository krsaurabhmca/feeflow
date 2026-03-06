<?php
require_once 'api_helper.php';
$institute = authenticate();

$institute_id = $institute['id'];

// Total Students
$stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE institute_id = ?");
$stmt->execute([$institute_id]);
$total_students = $stmt->fetchColumn();

// Total Collection (Current Month)
$month_start = date('Y-m-01');
$stmt = $pdo->prepare("SELECT SUM(amount) FROM fees WHERE institute_id = ? AND payment_date >= ?");
$stmt->execute([$institute_id, $month_start]);
$monthly_collection = $stmt->fetchColumn() ?: 0;

// Recent Transactions
$stmt = $pdo->prepare("SELECT f.*, s.name as student_name FROM fees f JOIN students s ON f.student_id = s.id WHERE f.institute_id = ? ORDER BY f.created_at DESC LIMIT 5");
$stmt->execute([$institute_id]);
$recent_transactions = $stmt->fetchAll();

sendResponse(true, "Dashboard stats", [
    "total_students" => $total_students,
    "monthly_collection" => $monthly_collection,
    "recent_transactions" => $recent_transactions
]);
?>
