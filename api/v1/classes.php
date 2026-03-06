<?php
require_once 'api_helper.php';
$institute = authenticate();
$institute_id = $institute['id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE institute_id = ? ORDER BY class_name ASC");
    $stmt->execute([$institute_id]);
    $classes = $stmt->fetchAll();
    sendResponse(true, "Classes list", $classes);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    if (!$data)
        $data = (object)$_POST;

    if (!isset($data->class_name)) {
        sendResponse(false, "Class/Course name is required", null, 400);
    }

    $stmt = $pdo->prepare("INSERT INTO classes (institute_id, class_name) VALUES (?, ?)");
    if ($stmt->execute([$institute_id, $data->class_name])) {
        sendResponse(true, "Class added successfully", ["id" => $pdo->lastInsertId()]);
    }
    else {
        sendResponse(false, "Failed to add class");
    }
}
?>

