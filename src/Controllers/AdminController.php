<?php
// AdminController.php
namespace App\Controllers;

use App\Constants\Application;
use App\Core\BaseController;
use App\Core\Csrf;
use App\Core\Middleware;
use App\Core\SystemHealth;
use App\Models\GameModel;
use App\Models\SystemSettingsModel;
use App\Models\UserModel;

class AdminController extends BaseController {
    public function __construct() {
        // Make sure that only Admins have access
        Middleware::admin();
    }

    // Dashboard
    public function dashboard(): void {
        $stats = [
            'users_total' => UserModel::countAll(), 
            'users_active' => UserModel::countByStatus('active'), 
            'users_inactive' => UserModel::countByStatus('inactive'), 
            'admins_total' => UserModel::countByRole('admin'), 
            'games_total' => GameModel::countAll(), 
            'games_waiting' => GameModel::countByStatus('waiting'), 
            'games_active' => GameModel::countByStatus('running'), 
            'games_finished' => GameModel::countByStatus('finished'), 
        ];

        $this->render(
            'admin/dashboard', 
            [
                'stats' => $stats
            ]
        );
    }

    // Users - List all users
    public function listUsers(): void {
        $users = UserModel::all();
        $this->render(
            'admin/users/list', 
            [
                'users' => $users
            ]
        );
    }

    // Users -Delete user
    public function deleteUser(string $user_id): void {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            http_response_code(403);
            die('Invalid CSRF token');
        }

        $user = UserModel::findById($user_id);
        if ($user) {
            $user->delete();
        }

        header('Location: /admin/users');
        exit;
    }

    // Users - Edit user
    public function editUser(string $user_id): void {
        $user = UserModel::findById($user_id);
        if (!$user) {
            http_response_code(404);
            die('User not found');
        }

        $this->render(
            'admin/users/edit', 
            [
                'user' => $user
            ]
        );
    }

    // Users - Update user
    public function updateUser(string $user_id): void {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            http_response_code(403);
            die('Invalid CSRF token');
        }

        $user = UserModel::findById($user_id);
        if (!$user) {
            http_response_code(404);
            die('User not found');
        }

        $username = $_POST['username'] ?? $user->getUsername();
        $email = $_POST['email'] ?? $user->getEmail();
        $role = $_POST['role'] ?? $user->getRole();

        $user->updateProfile($username, $email, $role);

        header('Location /admin/users');
        exit;
    }

    // Games - List all games
    public function listGames(): void {
        $games = GameModel::getAllGames();
        $this->render(
            'admin/games/list', 
            [
                'games' => $games
            ]
        );
    }

    // Games - Update game
    public function updateGame($game_id): void {
        if (!Csrf::validate($_POST['_csrf_token'])) {
            http_response_code(403);
            die('Invalid CSRF token');
        }

        $game = GameModel::findById($game_id);
        if (!$game) {
            http_response_code(404);
            die('Game not found');
        }

        $name = $_POST['name'] ?? $game->getName();
        $status = $_POST['status'] ?? $game->getStatus();

        // ToDo: Fix this -> update(3 arguments)
        //$game->update($name, $status);

        header('Location /admin/games');
        exit;
    }

    // Games - Delete game
    public function deleteGame($game_id): void {
        if (!Csrf::validate($_POST['_csrf_token'])) {
            http_response_code(403);
            die('Invalid CSRF token');
        }

        $game = GameModel::findById($game_id);
        if ($game) {
            $game->delete();
        }

        header('Location /admin/games');
        exit;
    }

    // System Settings - Overview
    public function systemSettings(): void {
        $system_settings = SystemSettingsModel::findSystemSettings();
        $maintenance_messages = [
            'system.settings.maintenance.message.000.title', 
            'system.settings.maintenance.message.001.title', 
            'system.settings.maintenance.message.002.title', 
            'system.settings.maintenance.message.003.title', 
        ]; 
        $notice_messages = [
            'system.settings.system.notice.000.title', 
            'system.settings.system.notice.001.title', 
            'system.settings.system.notice.002.title', 
            'system.settings.system.notice.003.title', 
        ]; 
        $this->render(
            'admin/system/settings', 
            [
                'system_settings' => $system_settings, 
                'maintenance_messages' => $maintenance_messages, 
                'notice_messages' => $notice_messages, 
            ]
        );
    }

    // System Settings - Update
    public function updateSystemSettings() {
        if ($_SERVER[Application::REQUEST_METHOD] === Application::REQUEST_METHOD_POST) {
            $system_settings = SystemSettingsModel::findSystemSettings(); 
            $system_settings->updateBooleansToFalse(); 
            $system_settings->updateFromArray($_POST); 
            $system_settings->update();
            return $this->jsonClean(
                [
                    'success' => true, 
                    'status' => 'ok', 
                    'message' => 'Success', 
                ], 200
            );
        }
        return $this->jsonClean(
            [
                'success' => false, 
                'status' => 'fail', 
                'error' => 'Error'
            ], 400
        );
    }

    // System Health - Overview
    public function systemHealth(): void {
        $overall = SystemHealth::getStatus();
        $database = SystemHealth::getDatabaseDetails(); 
        $environment = SystemHealth::getEnvironmentDetails(); 
        $game = SystemHealth::getGameDetails(); 
        $this->render(
            'admin/system/health', 
            [
                'overall' => $overall, 
                'database' => $database, 
                'environment' => $environment, 
                'game' => $game
            ]
        ); 
    }
}