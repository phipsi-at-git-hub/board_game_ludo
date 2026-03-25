<?php
// src/Controller/ApiGameController.php
namespace App\Controllers;

use App\Constants\Application;
use App\Core\BaseController;
use App\Core\Auth;
use App\Models\GameModel;
use DomainException;

class ApiGameController extends BaseController {
    // API - Game - State for Frontend
    public function state() {
        try {
            $game_id = $_POST[Application::GAME_ID] ?? null;
            $game = GameModel::findById($game_id);
            if (!$game) {
                return $this->jsonClean(['success' => false, 'error' => 'Game not found'], 404);
            }

            return $this->jsonClean([
                'success' => true,
                'state' => $game->getGameStateSnapshot()
            ]);
        } catch (\Throwable $e) {
            return $this->jsonClean([
                'success' => false, 
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // API - Game - Roll dice
    public function rollDice() {
        try {
            $game_id = $_POST[Application::GAME_ID] ?? null;
            $game = GameModel::findById($game_id);
            $user = Auth::user();

            if (!$game) return $this->jsonClean(['success' => false, 'error' => 'Game not found'], 404);

            if ($game->getCurrentPlayer()->getUserId() !== $user->getId()) {
                return $this->jsonClean(['success' => false, 'error' => 'Not your turn'], 403);
            }

            $game->rollDice($user->getId());

            return $this->jsonClean([
                'success' => true,
                'state' => $game->getGameStateSnapshot()
            ]);
        } catch (\Throwable $e) {
            return $this->jsonClean([
                'success' => false, 
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // API - Game - Returns possible moves for players
    public function getAvailableMoves() {
        try {
            $game_id = $_POST[Application::GAME_ID] ?? null;
            $game = GameModel::findById($game_id);
            $user = Auth::user();

            if (!$game) return $this->jsonClean(['success' => false, 'error' => 'Game not found'], 404);

            $current_roll = $game->getStateModel()->getCurrentDiceRoll();
            if ($current_roll === null) {
                return $this->jsonClean(['success' => false, 'error' => 'Dice not rolled yet'], 406);
            }

            $moves = $game->getAvailableMoves($user->getId(), $current_roll);

            // If no moves are available, offer at least one passing move
            if (empty($moves)) {
                $moves[] = [
                    Application::DTO_IS_PASS => true,
                    Application::DTO_FIGURE_INDEX => null,
                    Application::DTO_TO => null
                ];
            }

            return $this->jsonClean(['success' => true, 'moves' => $moves]);
        } catch (\Throwable $e) {
            return $this->jsonClean([
                'success' => false, 
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // API - Game - Execute given move for player
    public function applyMove() {
        $temp = $_POST;
        try {
            $game_id = $_POST[Application::GAME_ID] ?? null;
            $move = json_decode($_POST[Application::DTO_MOVE] ?? '[]', true);
            $game = GameModel::findById($game_id);
            $user = Auth::user();

            if (!$game) return $this->jsonClean(['success' => false, 'error' => 'Game not found'], 404);

            if ($game->getCurrentPlayer()->getUserId() !== $user->getId()) {
                return $this->jsonClean(['success' => false, 'error' => 'Not your turn'], 400);
            }

            try {
                if (!empty($move[Application::DTO_IS_PASS])) {
                    $game->passTurn($user->getId());
                } else {
                    $game->applyMove($user->getId(), $move);
                }

                return $this->jsonClean([
                    'success' => true,
                    'state' => $game->getGameStateSnapshot()
                ]);
            } catch (DomainException $e) {
                return $this->jsonClean([
                    'success' => false, 
                    'error' => $e->getMessage(), 
                    'move' => $move
                    ], 400);
            }
        } catch (\Throwable $e) {
            return $this->jsonClean([
                'success' => false, 
                'error' => $e->getMessage(), 
                'move' => $temp
            ], 400);
        }
    }

    // API - Game - Passes turn for player
    public function passTurn() {
        try {} catch (\Throwable $e) {
            return $this->jsonClean([
                'success' => false, 
                'error' => $e->getMessage()
            ], 400);
        }
        $game_id = $_POST[Application::GAME_ID] ?? null;
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        if (!$game) return $this->jsonClean(['success' => false, 'error' => 'Game not found'], 404);

        if ($game->getCurrentPlayer()->getUserId() !== $user->getId()) {
            return $this->jsonClean(['success' => false, 'error' => 'Not your turn'], 400);
        }

        $game->passTurn($user->getId());

        return $this->jsonClean([
            'success' => true,
            'state' => $game->getGameStateSnapshot()
        ]);
    }
}