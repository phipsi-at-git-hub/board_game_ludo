<?php
// routes.php
// Routes Config

use App\Core\Router;
use App\Core\Middleware;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\GameController;
use App\Controllers\AccountController;

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

// Profile and Account
$router->get('/account', [AccountController::class, 'profile'], [fn() => Middleware::auth()]);
$router->post('/account/update', [AccountController::class, 'updateProfile'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);
$router->post('/account/password', [AccountController::class, 'changePassword'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);
$router->post('/account/delete', [AccountController::class, 'deleteAccount'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);

// Password - Forgot password and reset
$router->get('/forgot-password', [AccountController::class, 'showForgotPassword'], [fn() => Middleware::guest()]);
$router->post('/forgot-password', [AccountController::class, 'sendResetLink'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);
$router->get('/reset-password/{token}', [AccountController::class, 'showResetForm'], [fn() => Middleware::guest()]);
$router->post('/reset-password/{token}', [AccountController::class, 'resetPassword'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);

// Game - Lobby
$router->get('/lobby', [GameController::class, 'lobby'], [fn() => Middleware::auth()]);

// Game - Create game
$router->get('/game/create', [GameController::class, 'create'], [fn() => Middleware::auth()]);
$router->post('/game/create', [GameController::class, 'create'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);

// Game - Join game
$router->get('/game/{id}', [GameController::class, 'view'], [fn() => Middleware::auth()]);
$router->post('/game/{id}/join', [GameController::class, 'join'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);

return $router;
