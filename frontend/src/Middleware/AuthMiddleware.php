<?php

namespace App\Middleware;

use App\Service\AuthService;

class AuthMiddleware
{
    public static function handle(): bool
    {
        $token = $_COOKIE['token'] ?? null;

        if (!$token || !AuthService::isValidToken($token)) {
            // Redirect unauthorized users to login page
            header("Location: /prihlasenie");
            return false;
        }

        return true;
    }
}