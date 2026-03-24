<?php
// src/Controller/ApiGameController.php
namespace App\Controllers;

use App\Constants\Application;
use App\Core\BaseController;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\GameModel;
use DomainException;

class ApiGameController extends BaseController
{
    // API - Game - State for Frontend
    public function state()
    {
        /*
        $this->jsonClean([
            'game_id' => $game_id
        ]);
        */
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            return $this->jsonClean(['success' => false, 'error' => 'Invalid CSRF token']);
        }

        $game_id = $_POST[Application::GAME_ID] ?? null;
        if (!$game_id) {
            return $this->jsonClean(['success' => false, 'error' => 'Missing game_id']);
        }

        $game = GameModel::findById($game_id);
        if (!$game) {
            return $this->jsonClean(['success' => false, 'error' => 'Game not found']);
        }

        return $this->jsonClean([
            'success' => true,
            'state' => $game->getGameStateSnapshot()
        ]);
    }

    // API - Game - Roll dice
    public function rollDice()
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            return $this->jsonClean(['success' => false, 'error' => 'Invalid CSRF token']);
        }

        $game_id = $_POST[Application::GAME_ID] ?? null;
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        if (!$game) return $this->jsonClean(['success' => false, 'error' => 'Game not found']);

        if ($game->getCurrentPlayer()->getUserId() !== $user->getId()) {
            return $this->jsonClean(['success' => false, 'error' => 'Not your turn']);
        }

        $game->rollDice($user->getId());

        return $this->jsonClean([
            'success' => true,
            'state' => $game->getGameStateSnapshot()
        ]);
    }

    // API - Game - Returns possible moves for players
    public function getAvailableMoves()
    {
        /*
        $game_id = $_POST[Application::GAME_ID] ?? null;
        $this->jsonClean([
            'game_id' => $game_id
        ]);
        */
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            return $this->jsonClean(['success' => false, 'error' => 'Invalid CSRF token']);
        }

        $game_id = $_POST[Application::GAME_ID] ?? null;
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        if (!$game) return $this->jsonClean(['success' => false, 'error' => 'Game not found']);

        $current_roll = $game->getStateModel()->getCurrentDiceRoll();
        if ($current_roll === null) {
            return $this->jsonClean(['success' => false, 'error' => 'Dice not rolled yet']);
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
    }

    // API - Game - Execute given move for player
    public function applyMove()
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            return $this->jsonClean(['success' => false, 'error' => 'Invalid CSRF token']);
        }

        $game_id = $_POST[Application::GAME_ID] ?? null;
        $move = json_decode($_POST[Application::DTO_MOVE] ?? '[]', true);
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        if (!$game) return $this->jsonClean(['success' => false, 'error' => 'Game not found']);

        if ($game->getCurrentPlayer()->getUserId() !== $user->getId()) {
            return $this->jsonClean(['success' => false, 'error' => 'Not your turn']);
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
            return $this->jsonClean(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // API - Game - Passes turn for player
    public function passTurn()
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            return $this->jsonClean(['success' => false, 'error' => 'Invalid CSRF token']);
        }

        $game_id = $_POST[Application::GAME_ID] ?? null;
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        if (!$game) return $this->jsonClean(['success' => false, 'error' => 'Game not found']);

        if ($game->getCurrentPlayer()->getUserId() !== $user->getId()) {
            return $this->jsonClean(['success' => false, 'error' => 'Not your turn']);
        }

        $game->passTurn($user->getId());

        return $this->jsonClean([
            'success' => true,
            'state' => $game->getGameStateSnapshot()
        ]);
    }
}