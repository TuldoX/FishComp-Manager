<?php

namespace App\MiddlewareFE;

use App\ServiceFE\AuthServiceFE;

class AuthMiddlewareFE
{
    public static function handle(): bool
    {
        $token = $_COOKIE['token'] ?? null;

        if (!$token || !AuthServiceFE::isValidToken($token)) {
            // Redirect unauthorized users to login page
            header("Location: /prihlasenie");
            return false;
        }

        return true;
    }
}