<?php 
// Infrastructure/Game/Persistence/MySqlGameRepository.php
namespace App\Infrastructure\Game\Persistence;

use App\Core\Database;
use App\Domain\Game\Game;
use App\Domain\Game\GameStatus;
use App\Domain\Game\State\GameState;
use App\Domain\Game\Rules\GameRules;
use App\Domain\Game\Persistence\GameRepository;
use App\Domain\Game\Persistence\GameSnapshotKey;
use PDO;
use Throwable;

final class MySqlGameRepository implements GameRepository {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function store(Game $game): void {
        $this->db->beginTransaction();

        try {
            $this->insertGame($game);
            $this->insertRuleSet($game);
            $this->insertGameState($game);
            if ($game->getState()->hasPlayers()) {
                $this->insertPlayer($game);
                $this->insertFigures($game);
            }
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Insert games
    public function insertGame(Game $game): void {
        // successful tested
        $stmt = $this->db->prepare("INSERT INTO games (id, created_by_user_id, status) Values (:id, :created_by_user_id, :status)");

        $stmt->execute([
            'id' => $game->getId(), 
            'created_by_user_id' => $game->getCreatedByUserId(), 
            'status' => $game->getStatus()->value,
        ]);
    }

    // Insert game_rule_set
    public function insertRuleSet(Game $game): void {
        $rules = $game->getRules();

        $stmt = $this->db->prepare(
            "INSERT INTO game_rule_set (
                game_id, 
                allow_bots, 
                extra_roll_on_six, 
                allow_stack_own_figures, 
                strict_goal_order, 
                start_field_must_be_cleared
            ) VALUES (
                :game_id, 
                :allow_bots, 
                :extra_roll_on_six, 
                :allow_stack_own_figures, 
                :strict_goal_order, 
                :start_field_must_be_cleared
            )"
        );

        $stmt->execute([
            'game_id' => $game->getId(), 
            'allow_bots' => (int) $rules->getAllowBots(), 
            'extra_roll_on_six' => (int) $rules->getExtraRollOnSix(), 
            'allow_stack_own_figures' => (int) $rules->getAllowStackOwnFigures(), 
            'strict_goal_order' => (int) $rules->getStrictGoalOrder(), 
            'start_field_must_be_cleared' => (int) $rules->getStartFieldMustBeCleared(),
        ]);
    }

    // Insert game_state
    public function insertGameState(Game $game): void {
        $state = $game->getState();

        $stmt = $this->db->prepare("INSERT INTO game_state (game_id, current_player_index) VALUES (:game_id, :current_player_index)");

        $stmt->execute([
            'game_id' => $game->getId(), 
            'current_player_index' => $state->getCurrentPlayerIndex(),
        ]);
    }

    // Insert game_state_players
    public function insertPlayer(Game $game): void {
        $stmt = $this->db->prepare("INSERT INTO game_state_players (game_id, user_id) VALUES (:game_id, :user_id)");

        foreach($game->getState()->getPlayers() as $player) {
            $stmt->execute([
                'game_id' => $game->getId(), 
                'user_id' => $player->getUserId(), 
            ]);
        }
    }

    // Insert game_state_figures
    public function insertFigures(Game $game): void {
        $stmt = $this->db->prepare(
            "INSERT INTO game_state_figures (
                game_id, 
                user_id, 
                figure_index, 
                position, 
                area
            ) VALUES (
                :game_id, 
                :user_id, 
                :figure_index, 
                :position, 
                :area
            )"
        );

        foreach ($game->getState()->getFigures() as $figure) {
            $stmt->execute([
                'game_id' => $game->getId(), 
                'user_id' => $figure->getPlayerId(), 
                'figure_index' => $figure->getFigureIndex(), 
                'position' => $figure->getPosition(), 
                'area' => $figure->getArea()->value, 
            ]);
        }
    }

    public function save(Game $game): void {
        $stmt = $this->db->prepare(
            "INSERT INTO games (id, created_by_user_id, status, rules, state)
             VALUES (:id, :created_by_user_id, :status, :rules, :state)"
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
        $stmt = $this->db->prepare("SELECT * FROM games WHERE id = :id");
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