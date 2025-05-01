<?php

require __DIR__ . '/../vendor/autoload.php';

use pwa\Controller\PrihlasenieController;
use pwa\Router\Router;
use pwa\Controller\HomePageController;

//echo "Hello, world from L17!";

// 1. vytvoríme inštanciu routra
$router = new Router();

// 2. nakonfigurujeme routy
// User management endpoints

$router->get('/',HomePageController::class, 'index');
$router->get('/prihlasenie',PrihlasenieController::class, 'index');
$router->get('/ulovky',\pwa\Controller\CatchesController::class, 'index');
$router->get('/pridanie_ulovku',\pwa\Controller\AddCatchController::class, 'index');
$router->get('/dashboard',\pwa\Controller\DashboardController::class, 'index');

// 3. zavoláme metódu dispatch na routri
$router->dispatch();
