<?php
require_once 'api_helper.php';

$data = json_decode(file_get_contents("php://input"));
if (!$data)
    $data = (object)$_POST;

if (!isset($data->name) || !isset($data->email) || !isset($data->password)) {
    sendResponse(false, "Name, email and password are required", null, 400);
}

// Check if email exists
$check = $pdo->prepare("SELECT id FROM institutes WHERE email = ?");
$check->execute([$data->email]);
if ($check->fetch()) {
    sendResponse(false, "Email already registered", null, 400);
}

$hashed = password_hash($data->password, PASSWORD_DEFAULT);
$api_key = bin2hex(random_bytes(32));

$stmt = $pdo->prepare("INSERT INTO institutes (name, email, password, api_key) VALUES (?, ?, ?, ?)");
if ($stmt->execute([$data->name, $data->email, $hashed, $api_key])) {
    $id = $pdo->lastInsertId();
    sendResponse(true, "Registration successful", ["id" => $id, "name" => $data->name, "api_key" => $api_key]);
}
else {
    sendResponse(false, "Failed to register");
}
?>
