<?php
// src/Controllers/Api/ApiGameController.php

namespace App\Controllers\Api;

use App\Constants\Application;
use App\Core\Application\App;
use App\Core\Auth\Auth;
use App\Core\BaseController;
use App\Core\Date\DateRange;
use App\Core\Dto\Game\GameContext;
use App\Core\Dto\Game\GameFilterContext;
use App\Core\Http\Response;
use App\Core\Logging\Logger;
use App\Models\Game\GameModel;
use App\Services\Game\GameFilterService;
use App\Services\Game\GameQueryService;
use App\Services\Game\GameService;

final class ApiGameController extends BaseController {
    private GameService $gameService; 

    public function __construct(App $app) { 
        parent::__construct($app); 
        $this->gameService = $app->resolve(GameService::class); 
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
        // Logging
        Logger::app()->info('Join game', ['user_id' => Auth::user()->getId(), 'game_id' => $game_id]);

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
        // Logging
        Logger::app()->info('Leave game', ['user_id' => Auth::user()->getId(), 'game_id' => $game_id]);
        
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
    // ToDo: Remove, since pausing a game is obsolete
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
                ['deleted' => true],
                'Game deleted'
            ), 201
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

    /**
     * gameFilterView
     *
     * @return void
     */
    public function gameFilterView(): void {
        // Parse log level if in POST body
        $user_id = $_POST['user_id'] ?? null; 
        $status = $_POST['status'] ?? []; 
        $relation = $_POST['user_relation'] ?? []; 
        $date_range = DateRange::fromString($_POST['date_range'] ?? ''); 

        $filter = new GameFilterService(
            userId: $user_id, 
            userRelations: $relation, 
            statuses: $status, 
            dateRange: $date_range, 
        ); 

        $query = new GameQueryService($filter); 
        $games = GameModel::findGames(
            player: true, 
            joins: $query->getJoins(), 
            conditions: $query->getConditions(), 
            params: $query->getParams(), 
        ); 

        // Build games filter context
        $context = GameFilterContext::fromFilter(
            $status, 
            $relation, 
            $date_range->getDateRangeAsString(), 
            $games
        ); 

        // Render filtered log entries
        $views = [
            'entries' => $this->renderView(
                'game/partials/entries',
                [
                    'games' => $games, 
                    'is_admin_view' => false, 
                ]
            )
        ];

        // Logging
        Logger::app()->debug('Api games logging filtered list view', ['user_id' => Auth::user()->getId()]);

        $this->jsonClean(
            Response::success(
                $context,
                'Games filter applied',
                $views
            )
        );
    }
}
