<?php

namespace App\Service;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService
{
    private static string $secretKey;

    /**
     * Initialize secret key
     */
    public static function initialize(string $secretKey): void
    {
        if (strlen($secretKey) < 32) {
            throw new Exception('JWT secret key too short (min 32 chars)');
        }
        self::$secretKey = $secretKey;
    }

    /**
     * Generate JWT token
     */
    public static function generateToken(array $payload): string
    {
        $issuedAt = time();
        $expiration = $issuedAt + 3600;

        $token = [
            'iat' => $issuedAt,
            'exp' => $expiration,
            'data' => $payload
        ];

        return JWT::encode($token, self::$secretKey, 'HS256');
    }

    /**
     * Decode token
     */
    public static function decodeToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key(self::$secretKey, 'HS256'));
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Check if token is valid (header or raw)
     */
    public static function isValidToken(string $auth): bool
    {
        $token = str_starts_with($auth, 'Bearer ')
            ? substr($auth, 7)
            : $auth;

        return self::decodeToken($token) !== null;
    }
}