<?php
// GameController.php
namespace App\Controllers;

use App\Core\Auth;

class GameController {
    public function lobby() {
        // Only for logged in users (secured through middleware)
        require __DIR__ . '/../Views/game/lobby.php';
    }

    public function create() {
        // Create a new game via POST
        echo 'Game created';
    }

    public function join(string $game_id) {
        // Player joins existing game
        echo 'Joining game: ' . htmlspecialchars($game_id);
    }

    public function view(string $game_id) {
        // View aa existing game
        echo 'Viewing game: ' . htmlspecialchars($game_id);
    }
}