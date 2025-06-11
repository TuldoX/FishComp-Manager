<?php

namespace App\MiddlewareFE;

use App\ServiceFE\AuthServiceFE;

class AuthMiddleware
{
    public static function handle(): bool
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!AuthServiceFE::isValidToken($authHeader)) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            return false;
        }

        return true;
    }
}