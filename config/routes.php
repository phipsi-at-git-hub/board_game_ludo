<?php
// config/routes.php
// Routes Config

use App\Controllers\AccountController;
use App\Controllers\AdminController;
use App\Controllers\Api\ApiAdminController; 
use App\Controllers\Api\ApiGameController;
use App\Controllers\Api\ApiGameEngineController;
use App\Controllers\AuthController;
use App\Controllers\GameController;
use App\Core\Middleware;
use App\Core\Router;
use App\Core\SystemSettings;

$router = new Router();

// If system settings system_enabled is false set up specific routes
if (SystemSettings::isOffline()) {
    // ToDo: add more routes for emergency / offline system
    $router->get('/emergency_login', [AuthController::class, 'showLogin'], [fn() => Middleware::guest()]);
    return $router; 
} 

// --- Guest routes ---
$router->get('/login', [AuthController::class, 'showLogin'], [fn() => Middleware::guest()]);
$router->post('/login', [AuthController::class, 'login'], [fn() => Middleware::guest(), fn() => Middleware::csrf()]);
$router->get('/register', [AuthController::class, 'showRegister'], [fn() => Middleware::guest()]);
$router->post('/register', [AuthController::class, 'register'], [fn() => Middleware::guest(), fn() => Middleware::csrf()]);
$router->get('/forgot-password', [AccountController::class, 'showForgotPassword'], [fn() => Middleware::guest()]);
$router->post('/forgot-password', [AccountController::class, 'sendResetLink'], [fn() => Middleware::guest(), fn() => Middleware::csrf()]);
$router->get('/reset-password/{token}', [AccountController::class, 'showResetForm'], [fn() => Middleware::guest()]);
$router->post('/reset-password/{token}', [AccountController::class, 'resetPassword'], [fn() => Middleware::guest(), fn() => Middleware::csrf()]);

// --- Authenticated routes ---
$router->post('/logout', [AuthController::class, 'logout'], [fn() => Middleware::auth(), fn() => Middleware::csrf()]);

$router->group('/account', function($group) {
    $group->get('', [AccountController::class, 'profile']);
    $group->put('/update', [AccountController::class, 'updateProfile'], [fn() => Middleware::csrf()]);
    $group->put('/password', [AccountController::class, 'changePassword'], [fn() => Middleware::csrf()]);
    $group->delete('/delete', [AccountController::class, 'deleteAccount'], [fn() => Middleware::csrf()]);
}, [fn() => Middleware::auth()]);

// --- Game routes ---
$router->get('/', [GameController::class, 'lobby'], [fn() => Middleware::auth()]);
$router->get('/lobby', [GameController::class, 'lobby'], [fn() => Middleware::auth()]);
$router->group('/game', function($group) {
    $group->get('/create', [GameController::class, 'create']);
    $group->get('/edit/{id}', [GameController::class, 'edit'], [fn() => Middleware::csrf()]);
    $group->get('/list', [GameController::class, 'list']);
    $group->get('/my_games', [GameController::class, 'usersGames']);
    $group->post('/store', [GameController::class, 'store'], [fn() => Middleware::csrf()]);
    $group->post('/update', [GameController::class, 'update'], [fn() => Middleware::csrf()]);
    $group->delete('/delete', [GameController::class, 'delete'], [fn() => Middleware::csrf()]);
    $group->get('/detail/{id}', [GameController::class, 'show']);
    $group->post('/join/{id}', [GameController::class, 'join'], [fn() => Middleware::csrf()]);
    $group->post('/start', [GameController::class, 'start'], [fn() => Middleware::csrf()]);
    $group->post('/pause', [GameController::class, 'pause'], [fn() => Middleware::csrf()]);
    $group->post('/cancel', [GameController::class, 'cancel'], [fn() => Middleware::csrf()]);
    $group->post('/reset', [GameController::class, 'reset'], [fn() => Middleware::csrf()]);
    $group->get('/play/{id}', [GameController::class, 'play']);
    $group->get('/state/{id}', [GameController::class, 'state']);
    $group->post('/roll', [GameController::class, 'roll'], [fn() => Middleware::csrf()]);
    $group->post('/move', [GameController::class, 'move'], [fn() => Middleware::csrf()]);
    $group->post('/leave/{id}', [GameController::class, 'leave'], [fn() => Middleware::csrf()]);
    $group->post('/create_solo_test', [GameController::class, 'createSoloTest'], [fn() => Middleware::csrf(), fn() => Middleware::admin()]);
    $group->post('/start_solo_test', [GameController::class, 'startSoloTest'], [fn() => Middleware::csrf(), fn() => Middleware::admin()]);
}, [fn() => Middleware::auth()]);

// --- Game Api routes
$router->group('/api/game', function($group) {
    $group->post('/join/{id}', [ApiGameController::class, 'join'], [fn() => Middleware::csrf()]); 
    $group->post('/leave/{id}', [ApiGameController::class, 'leave'], [fn() => Middleware::csrf()]); 
    $group->post('/start', [ApiGameController::class, 'start'], [fn() => Middleware::csrf()]); 
    $group->post('/pause', [ApiGameController::class, 'pause'], [fn() => Middleware::csrf()]); 
    $group->post('/cancel', [ApiGameController::class, 'cancel'], [fn() => Middleware::csrf()]); 
    $group->post('/reset', [ApiGameController::class, 'reset'], [fn() => Middleware::csrf()]); 
    $group->delete('/delete', [ApiGameController::class, 'delete'], [fn() => Middleware::csrf()]); 
}, [fn() => Middleware::auth()]); 

// --- Game Engine API routes
$router->group('/api/game-engine', function($group) {
    //$group->get('/state/{id}', [ApiGameController::class, 'state']);
    $group->post('/state', [ApiGameEngineController::class, 'state'], [fn() => Middleware::csrf()]);
    $group->post('/roll_dice', [ApiGameEngineController::class, 'rollDice'], [fn() => Middleware::csrf()]);
    $group->post('/get_available_moves', [ApiGameEngineController::class, 'getAvailableMoves'], [fn() => Middleware::csrf()]);
    $group->post('/apply_move', [ApiGameEngineController::class, 'applyMove'], [fn() => Middleware::csrf()]);
    $group->post('/pass_turn', [ApiGameEngineController::class, 'passTurn'], [fn() => Middleware::csrf()]);
}, [fn() => Middleware::auth()]);
$router->get('/api/game/health', [ApiGameEngineController::class, 'health']);

// --- Admin routes ---
$router->group('/admin', function($group) {
    $group->get('', [AdminController::class, 'dashboard']);
    $group->get('/users', [AdminController::class, 'listUsers']);
    $group->get('/user/edit/{id}', [AdminController::class, 'editUser']);
    $group->post('/user/edit/{id}', [AdminController::class, 'updateUser'], [fn() => Middleware::csrf()]);
    $group->delete('/user/{id}', [AdminController::class, 'deleteUser'], [fn() => Middleware::csrf()]);
    $group->get('/games', [AdminController::class, 'listGames']);
    $group->get('/game/show/{id}', [AdminController::class, 'show']);
    $group->get('/system/settings', [AdminController::class, 'systemSettings']);
    $group->post('/system/settings/update', [AdminController::class, 'updateSystemSettings'], [fn() => Middleware::csrf()]);
    $group->get('/system/health', [AdminController::class, 'systemHealth']);
}, [fn() => Middleware::auth(), fn() => Middleware::admin()]);

// --- Admin Api routes ---
$router->group('/api/admin', function($group) {
    $group->post('/system/settings/update', [ApiAdminController::class, 'updateSystemSettings'], [fn() => Middleware::csrf()]);
}, [fn() => Middleware::auth(), fn() => Middleware::admin()]);
return $router;
