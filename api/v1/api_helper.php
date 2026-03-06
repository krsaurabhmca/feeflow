<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../includes/config.php';

function sendResponse($status, $message, $data = null, $code = 200)
{
    http_response_code($code);
    echo json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data
    ]);
    exit();
}

function authenticate()
{
    global $pdo;
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
        // For simplicity, we'll use a straightforward token check. In production, use JWT or similar.
        $stmt = $pdo->prepare("SELECT id, name FROM institutes WHERE api_key = ?");
        $stmt->execute([$token]);
        $institute = $stmt->fetch();

        if ($institute) {
            return $institute;
        }
    }

    sendResponse(false, "Unauthorized access", null, 401);
}
?>
