<?php

namespace App\Middleware;

use App\Service\AuthService;

class AuthMiddleware
{
    public static function handle(): bool
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        return AuthService::isValidToken($authHeader);
    }
}