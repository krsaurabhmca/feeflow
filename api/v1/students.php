<?php
require_once 'api_helper.php';
$institute = authenticate();
$institute_id = $institute['id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $search = $_GET['search'] ?? '';
    $query = "SELECT s.*, c.class_name FROM students s LEFT JOIN classes c ON s.class_id = c.id WHERE s.institute_id = ?";
    $params = [$institute_id];

    if ($search) {
        $query .= " AND (s.name LIKE ? OR s.roll_no LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $query .= " ORDER BY s.name ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $students = $stmt->fetchAll();

    sendResponse(true, "Students list", $students);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->name) || !isset($data->class_id)) {
        sendResponse(false, "Name and Class ID are required", null, 400);
    }

    $stmt = $pdo->prepare("INSERT INTO students (institute_id, class_id, name, roll_no, phone, parent_name, session) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([
    $institute_id,
    $data->class_id,
    $data->name,
    $data->roll_no ?? null,
    $data->phone ?? null,
    $data->parent_name ?? null,
    $data->session ?? null
    ])) {
        sendResponse(true, "Student registered successfully", ["id" => $pdo->lastInsertId()]);
    }
    else {
        sendResponse(false, "Failed to register student");
    }
}
?>
