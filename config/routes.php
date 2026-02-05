<?php
// routes.php
// Routes Config

use App\Core\Router;
use App\Core\Middleware;
use App\Controllers\AccountController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\GameController;
use App\Controllers\HomeController;
use App\Controllers\MenuController;

$router = new Router();

// --- Guest routes ---
$router->get('/login', [AuthController::class, 'showLogin'], [fn() => Middleware::guest()]);
$router->post('/login', [AuthController::class, 'login'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);
$router->get('/register', [AuthController::class, 'showRegister'], [fn() => Middleware::guest()]);
$router->post('/register', [AuthController::class, 'register'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);
$router->get('/forgot-password', [AccountController::class, 'showForgotPassword'], [fn() => Middleware::guest()]);
$router->post('/forgot-password', [AccountController::class, 'sendResetLink'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);
$router->get('/reset-password/{token}', [AccountController::class, 'showResetForm'], [fn() => Middleware::guest()]);
$router->post('/reset-password/{token}', [AccountController::class, 'resetPassword'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);

// --- Authenticated routes ---
$router->get('/', [HomeController::class, 'index'], [fn() => Middleware::auth()]);
$router->get('/menu', [MenuController::class, 'index'], [fn() => Middleware::auth()]);
$router->get('/logout', [AuthController::class, 'logout'], [fn() => Middleware::auth()]);

$router->group('/account', function($group) {
    $group->get('', [AccountController::class, 'profile']);
    $group->put('/update', [AccountController::class, 'updateProfile'], [fn() => Middleware::csrf($_POST)]);
    $group->put('/password', [AccountController::class, 'changePassword'], [fn() => Middleware::csrf($_POST)]);
    $group->delete('/delete', [AccountController::class, 'deleteAccount'], [fn() => Middleware::csrf($_POST)]);
}, [fn() => Middleware::auth()]);

// --- Game routes ---
$router->get('/lobby', [GameController::class, 'lobby'], [fn() => Middleware::auth()]);
$router->group('/game', function($group) {
    $group->get('/single', [GameController::class, 'single']);
    $group->get('/create', [GameController::class, 'start']);
    $group->post('/create', [GameController::class, 'start'], [fn() => Middleware::csrf($_POST)]);
    $group->get('/{id}', [GameController::class, 'view']);
    $group->post('/{id}/join', [GameController::class, 'join'], [fn() => Middleware::csrf($_POST)]);
}, [fn() => Middleware::auth()]);

// --- Admin routes ---
$router->group('/admin', function($group) {
    $group->get('', [AdminController::class, 'dashboard']);
    $group->get('/users', [AdminController::class, 'listUsers']);
    $group->get('/users/edit/{id}', [AdminController::class, 'editUser']);
    $group->post('/users/edit/{id}', [AdminController::class, 'updateUser'], [fn() => Middleware::csrf($_POST)]);
    $group->delete('/users/{id}', [AdminController::class, 'deleteUser'], [fn() => Middleware::csrf($_POST)]);
    $group->get('/games', [AdminController::class, 'listGames']);
}, [fn() => Middleware::auth(), fn() => Middleware::admin()]);
return $router;


/*
use App\Core\Router;
use App\Core\Middleware;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\GameController;
use App\Controllers\AccountController;
use App\Controllers\MenuController;
use App\Controllers\AdminController;

$router = new Router();

// Home: logged in only
$router->get('/', [HomeController::class, 'index'], [fn() => Middleware::auth()]);

// Auth routes for guests
$router->get('/login', [AuthController::class, 'showLogin'], [fn() => Middleware::guest()]);
$router->post('/login', [AuthController::class, 'login'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);
$router->get('/register', [AuthController::class, 'showRegister'], [fn() => Middleware::guest()]);
$router->post('/register', [AuthController::class, 'register'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);
$router->post('/logout', [AuthController::class, 'logout'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);

// Account & Profile
$router->get('/account', [AccountController::class, 'profile'], [fn() => Middleware::auth()]);
$router->put('/account', [AccountController::class, 'updateProfile'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);
$router->put('/account/password', [AccountController::class, 'changePassword'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);
$router->delete('/account', [AccountController::class, 'deleteAccount'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);

// Password reset
$router->get('/forgot-password', [AccountController::class, 'showForgotPassword'], [fn() => Middleware::guest()]);
$router->post('/forgot-password', [AccountController::class, 'sendResetLink'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);
$router->get('/reset-password/{token}', [AccountController::class, 'showResetForm'], [fn() => Middleware::guest()]);
$router->post('/reset-password/{token}', [AccountController::class, 'resetPassword'], [fn() => Middleware::guest(), fn() => Middleware::csrf($_POST)]);

// Admin 
$router->group('/admin', function($admin) {

    // Dashboard
    $admin->get('/', [AdminController::class, 'dashboard']);

    // --- Users ---
    $admin->get('/users', [AdminController::class, 'listUsers']);
    $admin->delete('/users/{id}', [AdminController::class, 'deleteUser']);
    $admin->get('/users/edit/{id}', [AdminController::class, 'editUser']);
    $admin->post('/users/edit/{id}', [AdminController::class, 'updateUser']);

    // --- Games ---
    $admin->get('/games', [AdminController::class, 'listGames']);
    $admin->get('/games/create', [AdminController::class, 'createGame']);
    $admin->post('/games/create', [AdminController::class, 'createGame']);
    $admin->get('/games/edit/{id}', [AdminController::class, 'editGame']);
    $admin->post('/games/edit/{id}', [AdminController::class, 'updateGame']);
    $admin->delete('/games/{id}', [AdminController::class, 'deleteGame']);

}, [fn() => Middleware::auth(), fn() => Middleware::admin()]);

// Main menu
$router->get('/menu', [MenuController::class, 'index'], [fn() => Middleware::auth()]);

// Game - Lobby
$router->get('/lobby', [GameController::class, 'lobby'], [fn() => Middleware::auth()]);

// Game - Games
$router->post('/games', [GameController::class, 'store'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);
$router->get('/games/{id}', [GameController::class, 'show'], [fn() => Middleware::auth()]);
$router->delete('/games/{id}', [GameController::class, 'destroy'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);

// Games - Player actions
$router->post('/games/{id}/join', [GameController::class, 'join'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);
$router->post('/games/{id}/leave', [GameController::class, 'leave'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);
$router->post('/games/{id}/start', [GameController::class, 'start'], [fn() => Middleware::auth(), fn() => Middleware::csrf($_POST)]);

return $router;
*/