<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Controller\CompetitorController;
use App\Router\Router;
use App\Controller\RefereeController;
use App\Controller\AuthController;

$router = new Router();

$router->get('/referees/{refereeId:uuid}/competitors',RefereeController::class,'getCompetitors');
$router->post('/auth/referee', AuthController::class,'refereeLogin');
$router->get('/competitors/{competitorId:uuid}/catches', CompetitorController::class,'getCatches');

$router->dispatch();