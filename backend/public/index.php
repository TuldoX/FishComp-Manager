<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Controller\SpeciesController;
use App\Router\Router;
use App\Controller\RefereeController;
use App\Controller\AuthController;
use App\Controller\CompetitorController;
use App\Controller\CatchController;
use App\Service\AuthService;
use App\Middleware\AuthMiddleware;

// CORS headers
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- JWT secret initialization ---
$jwtSecret = getenv('JWT_SECRET_KEY');
if (!$jwtSecret) {
    throw new Exception('JWT secret key not configured');
}
AuthService::initialize($jwtSecret);
// --- end JWT secret initialization ---

// Instantiate router
$router = new Router();

// Public routes (no auth required)
$router->post('/api/auth/referee', AuthController::class, 'refereeLogin');

// Protected routes (require valid JWT)
$protectedRoutes = [
    ['GET', '/api/referees/{refereeId:uuid}/competitors'],
    ['GET', '/api/competitors/{competitorId:uuid}/catches'],
    ['DELETE', '/api/catches/{catchId:uuid}'],
    ['GET', '/api/species'],
    ['POST', '/api/catches'],
];

// Run middleware if the request matches a protected route
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

foreach ($protectedRoutes as [$routeMethod, $routePattern]) {
    if ($method === $routeMethod && Router::matchPattern($routePattern, $uri)) {
        if (!AuthMiddleware::handle()) {
            exit; // stop execution if not authorized
        }
        break;
    }
}

// Register routes
$router->get('/api/referees/{refereeId:uuid}/competitors', RefereeController::class, 'getCompetitors');
$router->post('/api/auth/referee', AuthController::class, 'refereeLogin');
$router->get('/api/competitors/{competitorId:uuid}/catches', CompetitorController::class, 'getCatches');
$router->delete('/api/catches/{catchId:uuid}', CatchController::class, 'deleteCatch');
$router->get('/api/species', SpeciesController::class, 'getSpecies');
$router->post('/api/catches', CatchController::class, 'createCatch');

// Dispatch
$router->dispatch();