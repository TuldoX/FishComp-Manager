<?php

namespace App\Middleware;

use App\Service\AuthService;

class AuthMiddleware
{
    public static function handle(): bool
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!AuthService::isValidToken($authHeader)) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            return false;
        }

        return true;
    }
}