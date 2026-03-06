<?php
require_once 'api_helper.php';
$institute = authenticate();
$institute_id = $institute['id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT name, email, phone, address, recognition_text, affiliation_text, receipt_prefix FROM institutes WHERE id = ?");
    $stmt->execute([$institute_id]);
    $data = $stmt->fetch();
    sendResponse(true, "Profile data", $data);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    if (!$data)
        $data = (object)$_POST;

    $action = $_GET['action'] ?? 'update';

    if ($action === 'change_password') {
        if (!isset($data->current_password) || !isset($data->new_password)) {
            sendResponse(false, "Current and new password are required", null, 400);
        }

        $stmt = $pdo->prepare("SELECT password FROM institutes WHERE id = ?");
        $stmt->execute([$institute_id]);
        $user = $stmt->fetch();

        if (!password_verify($data->current_password, $user['password'])) {
            sendResponse(false, "Incorrect current password", null, 403);
        }

        $hashed = password_hash($data->new_password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE institutes SET password = ? WHERE id = ?");
        if ($update->execute([$hashed, $institute_id])) {
            sendResponse(true, "Password changed successfully");
        }
        else {
            sendResponse(false, "Failed to change password");
        }
    }
    else {
        // Update general profile
        $stmt = $pdo->prepare("UPDATE institutes SET 
            name = ?, 
            phone = ?, 
            address = ?, 
            recognition_text = ?, 
            affiliation_text = ?, 
            receipt_prefix = ? 
            WHERE id = ?");

        if ($stmt->execute([
        $data->name,
        $data->phone ?? null,
        $data->address ?? null,
        $data->recognition_text ?? null,
        $data->affiliation_text ?? null,
        $data->receipt_prefix ?? null,
        $institute_id
        ])) {
            sendResponse(true, "Profile updated successfully");
        }
        else {
            sendResponse(false, "Failed to update profile");
        }
    }
}
?>
