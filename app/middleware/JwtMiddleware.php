<?php
class JwtMiddleware {
    public static function handle() {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;

        if (!$token) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token required']);
            exit;
        }

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $payload = JwtHelper::verify($token);
        if (!$payload) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid token']);
            exit;
        }
        return $payload;
    }
}