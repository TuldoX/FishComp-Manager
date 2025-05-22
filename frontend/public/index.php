<?php

require __DIR__ . '/../vendor/autoload.php';

// --- JWT secret initialization ---
$jwtSecret = getenv('JWT_SECRET_KEY');
if (!$jwtSecret) {
    throw new Exception('JWT secret key not configured');
}
\App\Service\AuthService::initialize($jwtSecret);
// --- end JWT secret initialization ---

use App\Controller\PrihlasenieController;
use App\Router\Router;
use App\Controller\HomePageController;
use App\Controller\CatchesController;
use App\Controller\AddCatchController;
use App\Controller\DashboardController;

//echo "Hello, world from L17!";

// 1. vytvoríme inštanciu routra
$router = new Router();

// 2. nakonfigurujeme routy
// User management endpoints

$router->get('/',HomePageController::class, 'index');
$router->get('/prihlasenie',PrihlasenieController::class, 'index');
$router->get('/ulovky',CatchesController::class, 'index');
$router->get('/pridanie_ulovku',AddCatchController::class, 'index');
$router->get('/dashboard',DashboardController::class, 'index');

// 3. zavoláme metódu dispatch na routri
$router->dispatch();
