<?php
require_once 'api_helper.php';
$institute = authenticate();
$institute_id = $institute['id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $student_id = $_GET['student_id'] ?? null;
    if ($student_id) {
        $stmt = $pdo->prepare("SELECT f.*, fc.category_name FROM fees f LEFT JOIN fee_categories fc ON f.fee_category_id = fc.id WHERE f.student_id = ? AND f.institute_id = ? ORDER BY f.payment_date DESC");
        $stmt->execute([$student_id, $institute_id]);
        $fees = $stmt->fetchAll();
        sendResponse(true, "Fee history", $fees);
    }
    else {
        $stmt = $pdo->prepare("SELECT f.*, s.name as student_name FROM fees f JOIN students s ON f.student_id = s.id WHERE f.institute_id = ? ORDER BY f.created_at DESC");
        $stmt->execute([$institute_id]);
        $fees = $stmt->fetchAll();
        sendResponse(true, "All fees", $fees);
    }
}

if ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents("php://input"));
        if (!$data)
            $data = (object)$_POST;

        if (!isset($data->student_id) || !isset($data->amount)) {
            sendResponse(false, "Student ID and Amount are required", null, 400);
        }

        // Receipt No Logic
        $inst_stmt = $pdo->prepare("SELECT receipt_prefix FROM institutes WHERE id = ?");
        $inst_stmt->execute([$institute_id]);
        $inst_data = $inst_stmt->fetch();
        $prefix = (isset($inst_data['receipt_prefix']) && $inst_data['receipt_prefix']) ? $inst_data['receipt_prefix'] : str_pad($institute_id, 3, '0', STR_PAD_LEFT) . '-';

        $last_rec = $pdo->prepare("SELECT COUNT(*) FROM fees WHERE institute_id = ?");
        $last_rec->execute([$institute_id]);
        $next_serial = str_pad($last_rec->fetchColumn() + 1, 4, '0', STR_PAD_LEFT);
        $receipt_no = $prefix . $next_serial;

        $stmt = $pdo->prepare("INSERT INTO fees (institute_id, student_id, fee_category_id, custom_fee_name, amount, payment_date, payment_method, receipt_no, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $params = [
            $institute_id,
            $data->student_id,
            $data->fee_category_id ?? null,
            $data->custom_fee_name ?? null,
            $data->amount,
            $data->payment_date ?? date('Y-m-d'),
            $data->payment_method ?? 'Cash',
            $receipt_no,
            $data->remarks ?? ''
        ];

        if ($stmt->execute($params)) {
            sendResponse(true, "Fee collected successfully", [
                "id" => $pdo->lastInsertId(),
                "receipt_no" => $receipt_no
            ]);
        }
        else {
            sendResponse(false, "Failed to collect fee. Database error.");
        }
    }
    catch (PDOException $e) {
        sendResponse(false, "Database Error: " . $e->getMessage(), null, 500);
    }
    catch (Exception $e) {
        sendResponse(false, "Error: " . $e->getMessage(), null, 500);
    }
}
?>

