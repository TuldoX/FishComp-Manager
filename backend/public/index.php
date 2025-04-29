<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Controller\SpeciesController;
use App\Router\Router;
use App\Controller\RefereeController;
use App\Controller\AuthController;
use App\Controller\CompetitorController;
use App\Controller\CatchController;

$router = new Router();

$router->get('/referees/{refereeId:uuid}/competitors',RefereeController::class,'getCompetitors');
$router->post('/auth/referee', AuthController::class,'refereeLogin');
$router->get('/competitors/{competitorId:uuid}/catches', CompetitorController::class,'getCatches');
$router->delete('/catches/{catchId:uuid}', CatchController::class,'deleteCatch');
$router->get('/species', SpeciesController::class,'getSpecies');
$router->post('/catches', CatchController::class,'createCatch');

$router->dispatch();