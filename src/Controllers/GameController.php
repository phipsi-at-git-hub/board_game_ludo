<?php
// GameController.php
namespace App\Controllers;

use App\Core\Auth;

class GameController {
    public function single() {
        echo 'Single';
    }

    public function lobby() {
        // Only for logged in users (secured through middleware)
        require __DIR__ . '/../Views/game/lobby.php';
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

    public function start (string $game_id) {
        echo 'Start';
    }

    public function destroy(string $game_id) {
        echo 'destroy';
    }
}