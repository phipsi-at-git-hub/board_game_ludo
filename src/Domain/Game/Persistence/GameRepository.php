<?php
// src/Domain/Game/GameRepository.php
namespace App\Domain\Game\Persistence;

use App\Domain\Game\Game;

interface GameRepository {
    public function save(Game $game): void;
    public function find(string $game_id): ?Game;
}