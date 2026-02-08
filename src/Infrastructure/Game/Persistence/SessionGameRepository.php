<?php
// src/Infrastructure/Game/SessionGameRepository.php
namespace App\Infrastructure\Game\Persistence;

use App\Domain\Game\Game;
use App\Domain\Game\GameStatus;
use App\Domain\Game\GameState;
use App\Domain\Game\Persistence\GameRepository;
use App\Domain\Game\Persistence\GameSnapshotKey;
use App\Domain\Game\Rules\GameRules;

final class SessionGameRepository implements GameRepository {
    public function save(Game $game): void {
        $_SESSION[GameSnapshotKey::GAMES][$game->getId()] = [
            GameSnapshotKey::ID => $game->getId(), 
            GameSnapshotKey::CREATED_BY_USER_ID => $game->getCreatedByUserId(), 
            GameSnapshotKey::STATUS => $game->getStatus()->value, 
            GameSnapshotKey::RULES => $game->getRules()->toArray(),
            GameSnapshotKey::STATE => $game->getState()->toArray(),
        ];
    }

    public function find(string $game_id): ?Game {
        if (!isset($_SESSION[GameSnapshotKey::GAMES][$game_id])) {
            return null;
        }

        $data = $_SESSION[GameSnapshotKey::GAMES][$game_id];

        return Game::restore(
            id: $game_id, 
            created_by_user_id: $data[GameSnapshotKey::CREATED_BY_USER_ID], 
            status: GameStatus::from($data[GameSnapshotKey::STATUS]), 
            rules: new GameRules($data[GameSnapshotKey::RULES]), 
            state: GameState::fromArray($data[GameSnapshotKey::STATE]),
        );
    }
}