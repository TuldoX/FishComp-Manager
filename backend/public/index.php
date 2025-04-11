<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;

use App\Controller\RefereeController;

$router = new Router();
// TODO: konfigurácia endpointov

$router->get('/referees/1/competitors',RefereeController::class,'read');

$router->dispatch();