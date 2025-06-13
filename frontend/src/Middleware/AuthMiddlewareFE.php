<?php

namespace App\Middleware;

use App\Service\AuthServiceFE;

class AuthMiddlewareFE
{
    public static function handle(): bool
    {
        $token = $_COOKIE['token'] ?? null;

        if (!$token || !AuthServiceFE::isValidToken($token)) return false;
        return true;
    }
}