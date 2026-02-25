<?php
// GameController.php
namespace App\Controllers;

use App\Constants\Application;
use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Csrf;
use App\Models\GameModel;

class GameController extends BaseController {
    public function single() {
        echo 'Single';
    }

    public function lobby() {
        // Only for logged in users (secured through middleware)
        $this->render(
            'game/lobby'
        );
    }

    public function create() {
        $this->render(
            'game/create'
        );
    }

    // Create a new game via POST
    public function store() {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            http_response_code(403);
            die('Invalid CSRF token');
        }

        if ($_SERVER[Application::REQUEST_METHOD] === Application::REQUEST_METHOD_POST && $_POST[Application::GAME_NAME] !== '') {
            $game_options = [
                Application::IS_PRIVATE => ($_POST[Application::IS_PRIVATE]), 
                Application::IS_LOCKED => ($_POST[Application::IS_LOCKED]), 
            ];

            $rule_set = [
                Application::ALLOW_BOTS => ($_POST[Application::ALLOW_BOTS]),
                Application::EXTRA_ROLL_LIMIT => ($_POST[Application::EXTRA_ROLL_LIMIT]),
                Application::ALLOW_STACK_OWN_FIGURES => ($_POST[Application::ALLOW_STACK_OWN_FIGURES]),
                Application::STRICT_GOAL_ORDER => ($_POST[Application::STRICT_GOAL_ORDER]),
                Application::START_FIELD_MUST_BE_CLEARED => ($_POST[Application::START_FIELD_MUST_BE_CLEARED]),
            ];
            
            $game_id = (new GameModel())->create($_SESSION[Application::USER_ID], $_POST[Application::GAME_NAME], $game_options, $rule_set);

            header("Location: /game/detail/$game_id");
            exit;
        }
        
        $this->render(
            'game/create'
        );
    }

    public function list(): void {
        $games = GameModel::getAllOpenGames();
        
        $this->render(
            'game/list', 
            [
                'games' => $games
            ]
        );

        //require __DIR__ . '/../Views/game/list.php';
    }

    public function show(string $game_id) {
        // View an existing game
        $game = GameModel::findById($game_id);

        if (!$game) {
            http_response_code(404);
            die('Game not found');
        }

        $this->render(
            'game/show', 
            [
                'game' => $game, 
            ]
        );
    }

    public function join(string $game_id) {
        // Player joins existing game
        echo 'Joining game: ' . htmlspecialchars($game_id);
    }

    public function leave(string $game_id) {
        echo 'leave';
    }

    public function start(string $game_id) {
        echo 'Start';
    }

    public function destroy(string $game_id) {
        echo 'destroy';
    }

    // Delete game
    public function delete(): void {
        $game_id = $_POST['game_id'];
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        $is_owner = $game->getCreatedByUserId() === $user->getId();
        $is_admin = $user->isAdmin();

        if (!$is_admin && !($is_owner && $game->isWaiting())) {
            http_response_code(403);
            exit ('Unauthorized');
        }

        $game->cancelGame();

        header('Location: /game/list');
    }
}