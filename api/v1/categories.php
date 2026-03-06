<?php
require_once 'api_helper.php';
$institute = authenticate();
$institute_id = $institute['id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM fee_categories WHERE institute_id = ? ORDER BY category_name ASC");
    $stmt->execute([$institute_id]);
    $categories = $stmt->fetchAll();
    sendResponse(true, "Fee categories", $categories);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!$data)
        $data = (object)$_POST;

    if (!isset($data->category_name)) {
        sendResponse(false, "Category name is required", null, 400);
    }

    $stmt = $pdo->prepare("INSERT INTO fee_categories (institute_id, category_name, description) VALUES (?, ?, ?)");
    if ($stmt->execute([$institute_id, $data->category_name, $data->description ?? ''])) {
        sendResponse(true, "Category added successfully", ["id" => $pdo->lastInsertId()]);
    }
    else {
        sendResponse(false, "Failed to add category");
    }
}
?>
