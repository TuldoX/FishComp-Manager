<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\PrihlasenieController;
use App\Controller\HomePageController;
use App\Controller\CatchesController;
use App\Controller\AddCatchController;
use App\Controller\DashboardController;
use App\RouterFE\RouterFE;
use App\Service\AuthServiceFE;
use App\Middleware\AuthMiddlewareFE;

// --- Initialize JWT secret ---
$jwtSecret = getenv('JWT_SECRET_KEY');
if (!$jwtSecret) {
    throw new Exception('JWT secret key not configured');
}
AuthServiceFE::initialize($jwtSecret);

// --- Set up router ---
$router = new RouterFE();

// Define public (unauthenticated) routes
$publicRoutes = [
    '/',
    '/prihlasenie',
];

// Current URI path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Run middleware for protected routes
if (!in_array($uri, $publicRoutes)) {
    if (!AuthMiddlewareFE::handle()) {
        exit; // Redirects already handled by middleware
    }
}

// Register routes
$router->get('/', HomePageController::class, 'index');
$router->get('/prihlasenie', PrihlasenieController::class, 'index');
$router->get('/ulovky', CatchesController::class, 'index');
$router->get('/pridanie_ulovku', AddCatchController::class, 'index');
$router->get('/dashboard', DashboardController::class, 'index');

// Dispatch request
$router->dispatch();