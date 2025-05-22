<?php

namespace App\Controller;
use App\View\HtmlView;
use App\Service\AuthService;
use Exception;

class AddCatchController
{
    public function index(): void
    {
        $htmlView = new HtmlView();
        $token = $_COOKIE['token'] ?? null;

        $authService = new AuthService();
        $jwtSecret = getenv('JWT_SECRET_KEY');
        if (!$jwtSecret) {
            throw new Exception('JWT secret key not configured');
        }
        AuthService::initialize($jwtSecret);

        if($token === null || !$authService::isValidToken('Bearer ' . $token)) {
            $htmlView->render('prihlasenie');
            return;
        }

        // Render the home page
        $htmlView->render('pridanie_ulovku');
    }

}