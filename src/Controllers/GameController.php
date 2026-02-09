<?php
// GameController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Domain\Game\Game;
use App\Domain\Game\Rules\GameRules;
use App\Domain\Game\Rules\GameRuleArrayNormalizer;
use App\Infrastructure\Game\Persistence\MySqlGameRepository;
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
        /*
        try {
            $rules = new GameRules($_POST);
            $user_id = Auth::user()->getId();
            $game = Game::create($user_id, $rules);

            // ToDo - persist game (Session / DB)
            flash('success', 'Game created successfully');
            redirect('/game/' .  $game->getId());
        } catch (Throwable $e) {
            echo "error";
            flash('error', $e->getMessage());
            back();
        }
            */
    }

    // Create a new game via POST
    public function store() {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            http_response_code(403);
            die('Invalid CSRF token');
        }

        //var_dump($_POST);

        $rules_array_from_post = $_POST['rules'];
        $normalized_rules_array = GameRuleArrayNormalizer::normalize($rules_array_from_post);
        $rules = new GameRules($normalized_rules_array ?? []);
        $user_id = Auth::user()->getId();
        $game = Game::create($user_id, $rules);

        $repo = new MySqlGameRepository();
        $repo->save($game);

        redirect('/game/' . $game->getId());
    }

    public function list() {
        $games = GameModel::all();
        require __DIR__ . '/../Views/game/list.php';
    }

    public function show(string $game_id) {
        // View aa existing game
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