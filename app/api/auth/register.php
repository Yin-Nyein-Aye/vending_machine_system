<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$db = (new Database())->connect();
$auth = new AuthController($db);

$data = json_decode(file_get_contents("php://input"), true);

if ($auth->register($data['username'], $data['password'])) {
    echo json_encode(["status" => "success"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Registration failed"]);
}
