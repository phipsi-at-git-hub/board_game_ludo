<?php
// routes.php
// Routes Config

use App\Core\Router;
use App\Core\Middleware;
use App\Controllers\AuthController;
use App\Controllers\HomeController;

$router = new Router();

// Home: For logged in user only
$router->get('/', [HomeController::class, 'index'], [fn() => Middleware::auth()]);

// Auth-Routes for guests
$router->get('/login', [AuthController::class, 'showLogin'], [fn() => Middleware::guest()]);
$router->post('/login', [AuthController::class, 'login'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);
$router->get('/register', [AuthController::class, 'showRegister'], [fn() => Middleware::guest()]);
$router->post('/register', [AuthController::class, 'register'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);

// Logout: For logged in user only
$router->get('/logout', [AuthController::class, 'logout'], [fn() => Middleware::auth()]);

return $router;
