<?php
// AdminController.php
namespace App\Controllers;

use App\Constants\Application;
use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Csrf;
use App\Core\Localization;
use App\Health\SystemHealth;
use App\Models\GameModel;
use App\Models\SystemSettingsModel;
use App\Models\UserModel;

class AdminController extends BaseController { 
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
            'admin/user/list', 
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
            'admin/user/edit', 
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

        $user->updateProfile($username, $email);

        header('Location /admin/users');
        exit;
    }

    // Games - List all games
    public function listGames(): void {
        $games = GameModel::getAllGames();
        $this->render(
            'admin/game/list', 
            [
                'games' => $games
            ]
        );
    }

    // Games - Game detail view 
    public function show(string $game_id) {
        // View an existing game
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        if (!$game) {
            http_response_code(404);
            die('Game not found');
        }

        $this->render(
            'admin/game/show', 
            [
                'game' => $game, 
            ]
        );
    }

    // Games - Update game
    public function updateGame(string $game_id): void {
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
    public function deleteGame(string $game_id): void {
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
                    'message' => Localization::get('application.response.messages.save.success'), 
                ], 200
            );
        }
        return $this->jsonClean(
            [
                'success' => false, 
                'status' => 'fail', 
                'message' => Localization::get('application.response.messages.save.failed'), 
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