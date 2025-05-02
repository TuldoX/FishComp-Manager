<?php
require __DIR__ . '/../vendor/autoload.php';

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

use App\Controller\SpeciesController;
use App\Router\Router;
use App\Controller\RefereeController;
use App\Controller\AuthController;
use App\Controller\CompetitorController;
use App\Controller\CatchController;

$router = new Router();

$router->get('/api/referees/{refereeId:uuid}/competitors',RefereeController::class,'getCompetitors');
$router->post('/api/auth/referee', AuthController::class,'refereeLogin');
$router->get('/api/competitors/{competitorId:uuid}/catches', CompetitorController::class,'getCatches');
$router->delete('/api/catches/{catchId:uuid}', CatchController::class,'deleteCatch');
$router->get('/api/species', SpeciesController::class,'getSpecies');
$router->post('/api/catches', CatchController::class,'createCatch');

$router->dispatch();