<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper {

    private static function getSecret() {
        return JWT_SECRET ?: "fallback_secret_123";
    }

    public static function generate($payload, $exp = 3600) {
        $payload['iat'] = time();
        $payload['exp'] = time() + $exp;
        return JWT::encode($payload, self::getSecret(), 'HS256');
    }

    public static function verify($token) {
        try {
            $decoded = JWT::decode($token, new Key(self::getSecret(), 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }
}