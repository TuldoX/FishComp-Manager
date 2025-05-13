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

        return JWT::encode($token, self::$secretKey, 'HS256');
    }

    public static function decodeToken(string $token): ?object {
        try {
            if (empty(self::$secretKey)) {
                throw new Exception('JWT secret key not initialized');
            }

            return JWT::decode($token, new Key(self::$secretKey, 'HS256'));
        } catch (Exception) {
            return null;
        }
    }
}