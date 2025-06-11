<?php

namespace App\Controller;
use App\View\HtmlView;
use App\ServiceFE\AuthServiceFE;
use Exception;

class DashboardController
{
    public function index(): void
    {
        // Render the home page
        $htmlView = new HtmlView();

        $token = $_COOKIE['token'] ?? null;

        $authService = new AuthServiceFE();
        // $jwtSecret = getenv('JWT_SECRET_KEY');
        // if (!$jwtSecret) {
        //     throw new Exception('JWT secret key not configured');
        // }
        // AuthServiceFE::initialize($jwtSecret);

        if($token === null || !$authService::isValidToken('Bearer ' . $token)){
            $htmlView->render('prihlasenie');
            return;
        }

        $htmlView->render('dashboard');
    }

}