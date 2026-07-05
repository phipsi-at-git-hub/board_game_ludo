<?php
// src/Controllers/ApiGameController.php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Http\Response;
use App\Core\Dto\GameContext;
use App\Models\GameModel;
use DomainException;
use Throwable;

class ApiGameController extends BaseController {
    private function gameOr404(string $game_id): GameModel {
        $game = GameModel::findById($game_id);
        if (!$game) {
            $this->jsonClean(
                Response::error('Game not found'),
                404
            );
        }
        return $game;
    }

    private function context(GameModel $game): array {
        return GameContext::fromGame(
            $game,
            Auth::user()
        );
    }

    public function join(string $game_id): void {
        try {
            $game = $this->gameOr404($game_id);
            $game->join(Auth::user()->getId());
            $this->jsonClean(
                Response::success(
                    $this->context($game),
                    'Joined game'
                )
            );
        } catch (DomainException $e) {
            $this->jsonClean(
                Response::error($e->getMessage()),
                400
            );
        }
    }

    public function leave(string $game_id): void {
        try {
            $game = $this->gameOr404($game_id);
            $game->leave(Auth::user()->getId());
            $this->jsonClean(
                Response::success(
                    $this->context($game),
                    'Left game'
                )
            );
        } catch (DomainException $e) {
            $this->jsonClean(
                Response::error($e->getMessage()),
                400
            );
        }
    }

    public function start(string $game_id): void {
        try {
            $game = $this->gameOr404($game_id);
            $game->startGame();
            $this->jsonClean(
                Response::success(
                    $this->context($game),
                    'Game started'
                )
            );
        } catch (Throwable $e) {
            $this->jsonClean(
                Response::error($e->getMessage()),
                400
            );
        }
    }

    public function reset(string $game_id): void {
        try {
            $game = $this->gameOr404($game_id);
            $game->resetGame();
            $this->jsonClean(
                Response::success(
                    $this->context($game),
                    'Game reset'
                )
            );
        } catch (Throwable $e) {
            $this->jsonClean(
                Response::error($e->getMessage()),
                400
            );
        }
    }

    public function delete(string $game_id): void {
        try {
            $game = $this->gameOr404($game_id);
            $game->delete();
            $this->jsonClean(
                Response::success(
                    [],
                    'Game deleted'
                )
            );
        } catch (Throwable $e) {
            $this->jsonClean(
                Response::error($e->getMessage()),
                400
            );
        }
    }

    public function show(string $game_id): void {
        $game = $this->gameOr404($game_id);
        $this->jsonClean(
            Response::success(
                $this->context($game)
            )
        );
    }
}