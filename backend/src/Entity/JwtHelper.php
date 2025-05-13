<?php
namespace App\Entity;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtHelper {
    private static string $secretKey;

    public static function initialize(string $secretKey): void {
        if (strlen($secretKey) < 32) {
            throw new Exception('JWT secret key too short (min 32 chars)');
        }
        self::$secretKey = $secretKey;
        error_log("JWT initialized with key: " . substr($secretKey, 0, 3) . "...");
    }

    public static function generateToken(array $payload): string {
        if (empty(self::$secretKey)) {
            throw new Exception('JWT secret key not initialized');
        }

        $issuedAt = time();
        $expiration = $issuedAt + 3600; // 1 hour

        $token = [
            'iat' => $issuedAt,
            'exp' => $expiration,
            'data' => $payload
        ];

        error_log("Generating token for: " . json_encode($payload));
        return JWT::encode($token, self::$secretKey, 'HS256');
    }

    public static function decodeToken(string $token): ?object {
        try {
            if (empty(self::$secretKey)) {
                throw new Exception('JWT secret key not initialized');
            }

            return JWT::decode($token, new Key(self::$secretKey, 'HS256'));
        } catch (Exception $e) {
            error_log("JWT decode error: " . $e->getMessage());
            return null;
        }
    }
}