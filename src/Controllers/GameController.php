<?php
// GameController.php
namespace App\Controllers;

use App\Constants\Application;
use App\Core\Csrf;
use App\Models\GameModel;

class GameController {
    public function single() {
        echo 'Single';
    }

    public function lobby() {
        // Only for logged in users (secured through middleware)
        require __DIR__ . '/../Views/game/lobby.php';
    }

    public function create() {
        require __DIR__ . '/../Views/game/create.php';
    }

    // Create a new game via POST
    public function store() {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            http_response_code(403);
            die('Invalid CSRF token');
        }

        if ($_SERVER[Application::REQUEST_METHOD] === Application::REQUEST_METHOD_POST) {
            $rule_set = [
                Application::ALLOW_BOTS => ($_POST[Application::ALLOW_BOTS]),
                Application::EXTRA_ROLL_ON_SIX => ($_POST[Application::EXTRA_ROLL_ON_SIX]),
                Application::ALLOW_STACK_OWN_FIGURES => ($_POST[Application::ALLOW_STACK_OWN_FIGURES]),
                Application::STRICT_GOAL_ORDER => ($_POST[Application::STRICT_GOAL_ORDER]),
                Application::START_FIELD_MUST_BE_CLEARED => ($_POST[Application::START_FIELD_MUST_BE_CLEARED]),
            ];
            
            $game_id = (new GameModel())->create($_SESSION[Application::USER_ID], $rule_set);

            header("Location: /game/$game_id");
            exit;
        }
        
        require __DIR__ . '/../Views/game/create.php';
    }

    public function list() {
        $games = GameModel::all();
        require __DIR__ . '/../Views/game/list.php';
    }

    public function show(string $game_id) {
        // View an existing game
        echo "Game created<br/>";
        echo 'Viewing game: ' . htmlspecialchars($game_id);
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
}