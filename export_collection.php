<?php
require_once 'includes/config.php';
if (!is_logged_in())
    die("Unauthorized");

$institute_id = get_institute_id();
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Fetch Detailed Data for CSV
$stmt = $pdo->prepare("SELECT f.payment_date, f.receipt_no, s.name as student_name, s.roll_no, 
                      c.class_name, COALESCE(fc.category_name, f.custom_fee_name) as fee_type, 
                      f.payment_method, f.amount
                      FROM fees f
                      JOIN students s ON f.student_id = s.id
                      LEFT JOIN classes c ON s.class_id = c.id
                      LEFT JOIN fee_categories fc ON f.fee_category_id = fc.id
                      WHERE f.institute_id = ? AND f.payment_date BETWEEN ? AND ?
                      ORDER BY f.payment_date ASC, f.created_at ASC");
$stmt->execute([$institute_id, $start_date, $end_date]);
$data = $stmt->fetchAll();

// Set Headers for Download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Collection_Report_' . $start_date . '_to_' . $end_date . '.csv');

$output = fopen('php://output', 'w');

// Column Headers
fputcsv($output, ['Date', 'Receipt No', 'Student Name', 'Roll No', 'Class', 'Fee Type', 'Method', 'Amount (INR)']);

// Data Rows
foreach ($data as $row) {
    fputcsv($output, [
        $row['payment_date'],
        $row['receipt_no'],
        $row['student_name'],
        $row['roll_no'],
        $row['class_name'],
        $row['fee_type'],
        $row['payment_method'],
        number_format($row['amount'], 2, '.', '')
    ]);
}

fclose($output);
exit();
