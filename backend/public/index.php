<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;
use App\Controller\RefereeController;

$router = new Router();

$router->get('/referees/{refereeId:uuid}/competitors',RefereeController::class,'getCompetitors');

$router->dispatch();