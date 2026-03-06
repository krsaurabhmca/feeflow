<?php
require_once 'api_helper.php';

$data = json_decode(file_get_contents("php://input"));

// Safety fallback for some hosting environments
if (!$data) {
    $data = (object)$_POST;
}

if (!isset($data->email) || !isset($data->password)) {
    sendResponse(false, "Email and password are required", null, 400);
}

$stmt = $pdo->prepare("SELECT id, name, password, api_key FROM institutes WHERE email = ?");
$stmt->execute([$data->email]);
$institute = $stmt->fetch();

if ($institute && password_verify($data->password, $institute['password'])) {
    $api_key = $institute['api_key'];

    if (!$api_key) {
        $api_key = bin2hex(random_bytes(32));
        $update = $pdo->prepare("UPDATE institutes SET api_key = ? WHERE id = ?");
        $update->execute([$api_key, $institute['id']]);
    }

    unset($institute['password']);
    $institute['api_key'] = $api_key;

    sendResponse(true, "Login successful", $institute);
}
else {
    sendResponse(false, "Invalid email or password", null, 401);
}
?>
