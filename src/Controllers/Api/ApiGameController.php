<?php
// src/Controllers/Api/ApiGameController.php

namespace App\Controllers\Api;

use App\Constants\Application;
use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Dto\Game\GameContext;
use App\Core\Http\Response;
use App\Models\GameModel;
use App\Services\GameService;

final class ApiGameController extends BaseController {
    private GameService $gameService; 

    public function __construct() { 
        $this->gameService = new GameService(); 
    } 

    // Helper - Return game or send error json 
    private function requireGame(string $game_id): GameModel { 
        $game = GameModel::findById($game_id); 
        if (!$game) {
            $this->jsonClean(
                Response::error('Game not found'),
                404
            );
        }
        return $game;
    }

    // Context of the game - DTO of game
    private function context(GameModel $game): array { 
        return GameContext::fromGame(
            $game,
            Auth::user()
        );
    }

    /**
     * Generic game action handler
     */
    private function executeApiGameActions(string $game_id, callable $action, string $success_message): void {
        $game = $this->requireGame($game_id);
        $success = $action($game);
        if (!$success) {
            $this->jsonClean(
                Response::error('Action not allowed'),
                400
            );
        }

        $game = GameModel::findById($game->getId()); 
        $this->jsonClean(
            Response::success(
                $this->context($game),
                $success_message
            )
        );
    }

    /**
     * Generic game action with view in response
     */
    private function executeApiGameActionWithViews(string $game_id, callable $action, string $success_message, callable $view_builder): void {
        $game = $this->requireGame($game_id); 
        $success = $action($game); 
        if (!$success) {
            $this->jsonClean(
                Response::error('Action not allowed'), 400 
            ); 
        } 

        $game = GameModel::findById($game->getId()); 
        $this->jsonClean(
            Response::success(
                $this->context($game), 
                $success_message, 
                $view_builder($game) 
            )
        ); 
    }

    /**
     * Specific players partials for response
     */
    private function buildPlayersView(GameModel $game): array {
        return [
            'players' => $this->renderView(
                'game/partials/players', 
                [
                    'game' => $game, 
                    'current_user' => Auth::user() 
                ] 
            ) 
        ]; 
    }

    // Join game
    public function join(string $game_id): void {
        $this->executeApiGameActionWithViews(
            $game_id, 
            fn(GameModel $game) => $this->gameService->join(
                $game, 
                Auth::user() 
            ), 
            'Joined game', 
            fn(GameModel $game) => $this->buildPlayersView($game) 
        ); 
    }

    // Leave game
    public function leave(string $game_id): void {
        $this->executeApiGameActionWithViews(
            $game_id, 
            fn(GameModel $game) => $this->gameService->leave(
                $game, 
                Auth::user() 
            ), 
            'Left game', 
            fn(GameModel $game) => $this->buildPlayersView($game) 
        ); 
    }

    // Start game
    public function start(string $game_id): void {
        $this->executeApiGameActions(
            $game_id,
            fn(GameModel $game) =>
                $this->gameService->start(
                    $game,
                    Auth::user()
                ),
            'Game started'
        );
    }

    // Pause game
    public function pause(string $game_id): void {
        $this->executeApiGameActions(
            $game_id,
            fn(GameModel $game) =>
                $this->gameService->pause(
                    $game,
                    Auth::user()
                ),
            'Game paused'
        );
    }

    // Reset game
    public function reset(string $game_id): void {
        $this->executeApiGameActions(
            $game_id,
            fn(GameModel $game) =>
                $this->gameService->reset(
                    $game,
                    Auth::user()
                ),
            'Game reset'
        );
    }

    // Cancel game
    public function cancel(string $game_id): void {
        $this->executeApiGameActions(
            $game_id,
            fn(GameModel $game) =>
                $this->gameService->cancel(
                    $game,
                    Auth::user()
                ),
            'Game cancelled'
        );
    }

    // Delete game
    public function delete(): void {
        $game_id = $_POST[Application::GAME_ID] ?? null; 
        $game = $this->requireGame($game_id);
        $success = $this->gameService->delete(
            $game,
            Auth::user()
        );

        if (!$success) {
            $this->jsonClean(
                Response::error('Unable to delete game'),
                400
            );
        }

        $this->jsonClean(
            Response::success(
                [],
                'Game deleted'
            )
        );
    }

    // Show game
    public function show(string $game_id): void {
        $game = $this->requireGame($game_id);
        $this->jsonClean(
            Response::success(
                $this->context($game)
            )
        );
    }
}
