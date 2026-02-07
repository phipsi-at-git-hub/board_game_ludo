<?php
// AdminController.php
namespace App\Controllers;

use App\Core\Middleware;
use App\Core\Csrf;
use App\Domain\UserRole;
use App\Domain\Game\GameStatus;
use App\Models\UserModel;
use App\Models\GameModel;

class AdminController {
    public function __construct() {
        // Make sure that only Admins have access
        Middleware::admin();
    }

    // Dashboard
    public function dashboard(): void {
        $stats = [
            'users_total' => UserModel::countAll(), 
            'admins_total' => UserModel::countByRole(UserRole::ADMIN), 
            'games_total' => GameModel::countAll(), 
            'games_waiting' => GameModel::countByStatus(GameStatus::WAITING), 
            'games_active' => GameModel::countByStatus(GameStatus::ACTIVE), 
            'games_finished' => GameModel::countByStatus(GameStatus::FINISHED), 
        ];
        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    // Users - List all users
    public function listUsers(): void {
        $users = UserModel::all();
        require __DIR__ . '/../Views/admin/users/list.php';
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

        require __DIR__ . '/../Views/admin/users/edit.php';
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
        $games = GameModel::all();
        require __DIR__ . '/../Views/admin/games/list.php';
    }

    // Games - Create new game
    public function createGame(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
                http_response_code(403);
                die('Invalid CSFR token');
            }

            $name = $_POST['name'] ?? 'New Game';
            $game = GameModel::create($name);

            header('Location /admin/games/list');
            exit;
        }

        require __DIR__ . '/../Views/admin/games/create.php';
    }

    // Games - Edit game
    public function editGame(string $game_id): void {
        $game = GameModel::findById($game_id);
        if (!$game) {
            http_response_code(404);
            die('Game not found');
        }
        require __DIR__ . '/../Views/admin/games/edit.php';
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

        $game->update($name, $status);

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
}