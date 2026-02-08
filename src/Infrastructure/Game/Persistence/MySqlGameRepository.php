<?php 
// Infrastructure/Game/Persistence/MySqlGameRepository.php
namespace App\Infrastructure\Game\Persistence;

use App\Domain\Game\Game;
use App\Domain\Game\GameStatus;
use App\Domain\Game\GameState;
use App\Domain\Game\Rules\GameRules;
use App\Domain\Game\Persistence\GameRepository;
use App\Domain\Game\Persistence\GameSnapshotKey;
use PDO;

final class MySqlGameRepository implements GameRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(Game $game): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO games (id, status, rules, state)
             VALUES (:id, :status, :rules, :state)"
        );

        $stmt->execute([
            GameSnapshotKey::ID => $game->getId(),
            GameSnapshotKey::CREATED_BY_USER_ID => $game->getCreatedByUserId(), 
            GameSnapshotKey::STATUS => $game->getStatus()->value,
            GameSnapshotKey::RULES => json_encode($game->getRules()->toArray()),
            GameSnapshotKey::STATE => json_encode($game->getState()->toArray()),
        ]);
    }

    public function find(string $game_id): ?Game {
        $stmt = $this->pdo->prepare("SELECT * FROM games WHERE id = :id");
        $stmt->execute([GameSnapshotKey::ID => $game_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Game(
            id: $row[GameSnapshotKey::ID], 
            created_by_user_id: $row[GameSnapshotKey::CREATED_BY_USER_ID], 
            status: GameStatus::from($row[GameSnapshotKey::STATUS]), 
            rules: new GameRules(json_decode($row[GameSnapshotKey::RULES], true)), 
            state: GameState::fromArray(json_decode($row[GameSnapshotKey::STATE], true)), 
        );
    }
}