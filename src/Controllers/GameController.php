<?php
// GameController.php
namespace App\Controllers;

use App\Constants\Application;
use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Csrf;
use App\Models\GameModel;
use DomainException;

class GameController extends BaseController {
    // Lobby leads to game creation, games list and back
    public function lobby() {
        // Only for logged in users (secured through middleware)
        $this->render(
            'game/lobby'
        );
    }

    // Game creation form
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

    // Games list overview
    public function list(): void {
        $games = GameModel::getAllOpenGames();
        $user = Auth::user();
        
        $this->render(
            'game/list', 
            [
                'games' => $games, 
                'user' => $user
            ]
        );

        //require __DIR__ . '/../Views/game/list.php';
    }

    // Game detail view
    public function show(string $game_id) {
        // View an existing game
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        if (!$game) {
            http_response_code(404);
            die('Game not found');
        }

        $this->render(
            'game/show', 
            [
                'game' => $game, 
                'user' => $user
            ]
        );
    }

    // Game join 
    public function join(string $game_id) {
        // Player joins existing game

        $game = GameModel::findById($game_id);

        if (!$game) {
            http_response_code(404);
            die('Game not found');
        }

        try {
            $game->join(Auth::user()->getId());
        } catch (DomainException $e) {
            // ToDo: some exception handling
        }

        header("Location: /game/detail/$game_id");
        exit;
    }

    public function leave(string $game_id) {
        $game = GameModel::findById($game_id);

        if (!$game) {
            http_response_code(404);
            die('Game not found');
        }

        try {
            // ToDo: implement leaving game 
            $game->leave(Auth::user()->getId());
        } catch (DomainException $e) {
            // ToDo: some exception handling
        }

        header("Location: /game/detail/$game_id");
        exit;
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