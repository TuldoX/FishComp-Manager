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
use App\View\JsonView;

$viewer = new JsonView();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost");
    header("Access-Control-Allow-Methods: GET,POST,DELETE,OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    http_response_code(204);
    exit;
}


// --- JWT secret initialization ---
$jwtSecret = getenv('JWT_SECRET_KEY');
if (!$jwtSecret) {
    throw new Exception('JWT secret key not configured');
}

AuthService::initialize($jwtSecret);

$router = new Router();

// Public routes
$router->post('/api/auth/referee', AuthController::class, 'refereeLogin');

// Protected routes
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
            $viewer->render(['error' => 'Unauthorized'], 401);
            exit;
        }
        break;
    }
}

$router->get('/api/referees/{refereeId:uuid}/competitors', RefereeController::class, 'getCompetitors');
$router->get('/api/competitors/{competitorId:uuid}/catches', CompetitorController::class, 'getCatches');
$router->delete('/api/catches/{catchId:uuid}', CatchController::class, 'deleteCatch');
$router->get('/api/species', SpeciesController::class, 'getSpecies');
$router->post('/api/catches', CatchController::class, 'createCatch');

$router->dispatch();