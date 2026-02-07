<?php
// GameController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Domain\Game\Game;
use App\Domain\Game\Rules\GameRules;
use InvalidArgumentException;
use Throwable;

class GameController {
    public function single() {
        echo 'Single';
    }

    public function lobby() {
        // Only for logged in users (secured through middleware)
        require __DIR__ . '/../Views/game/lobby.php';
    }

    public function create() {
        echo "create";
        try {
            $rules = new GameRules($_POST);
            $game = Game::create($rules);

            // ToDo - persist game (Session / DB)
            flash('success', 'Game created successfully');
            redirect('/game/' .  $game->getId());
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function store() {
        // Create a new game via POST
        echo 'Game created';
    }

    public function show(string $game_id) {
        // View aa existing game
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