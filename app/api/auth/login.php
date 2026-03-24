<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../middleware/JwtMiddleware.php';

$db = (new Database())->connect();
$auth = new AuthController($db);

$data = json_decode(file_get_contents("php://input"), true);

if ($auth->login($data['username'], $data['password'])) {
    $payload = [
        "user_id" => $_SESSION['user_id'],
        "role" => $_SESSION['role'],
        "exp" => time() + 3600
    ];
    $token = Firebase\JWT\JWT::encode($payload, "secret_key", "HS256");
    echo json_encode(["token" => $token]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
}
