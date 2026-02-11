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
use App\Domain\Game\Rules\GameRuleKey;
use App\Domain\Game\State\FigureArea;
use App\Domain\Game\State\FigureState;
use App\Domain\Game\State\FigureStateKey;
use App\Domain\Game\State\GameStateKey;
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
        // Load base game
        $stmt = $this->db->prepare("SELECT * FROM games WHERE id = :id LIMIT 1");
        $stmt->execute([GameSnapshotKey::ID => $game_id]);
        $row_game = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row_game) {
            return null;
        }

        // Load game rule set
        $stmt = $this->db->prepare("SELECT * FROM game_rule_set WHERE game_id = :game_id LIMIT 1");
        $stmt->execute([GameSnapshotKey::GAME_ID => $game_id]);
        $row_rule_set = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        // Load game state
        $stmt = $this->db->prepare("SELECT * FROM game_state WHERE game_id = :game_id LIMIT 1");
        $stmt->execute([GameSnapshotKey::GAME_ID => $game_id]);
        $row_state = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        // Load game players
        $stmt = $this->db->prepare("SELECT * FROM game_state_players WHERE game_id = :game_id ORDER BY created_at ASC");
        $stmt->execute([GameSnapshotKey::GAME_ID => $game_id]);
        $row_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Load game figures
        $stmt = $this->db->prepare("SELECT * FROM game_state_figures WHERE game_id = :game_id");
        $stmt->execute([GameSnapshotKey::GAME_ID => $game_id]);
        $row_figures = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->hydrateGame(
            $row_game, 
            $row_rule_set, 
            $row_state, 
            $row_players, 
            $row_figures
        );
    }

    private function hydrateGame(array $row_game, array $row_rule_set, array $row_state, array $row_players, array $row_figures): Game {
        // GameRules
        $rules = new GameRules([
            GameRuleKey::ALLOW_BOTS => (bool) $row_rule_set[GameRuleKey::ALLOW_BOTS], 
            GameRuleKey::EXTRA_ROLL_ON_SIX => (bool) $row_rule_set[GameRuleKey::EXTRA_ROLL_ON_SIX], 
            GameRuleKey::ALLOW_STACK_OWN_FIGURES => (bool) $row_rule_set[GameRuleKey::ALLOW_STACK_OWN_FIGURES], 
            GameRuleKey::STRICT_GOAL_ORDER => (bool) $row_rule_set[GameRuleKey::STRICT_GOAL_ORDER], 
            GameRuleKey::START_FIELD_MUST_BE_CLEARED => (bool) $row_rule_set[GameRuleKey::START_FIELD_MUST_BE_CLEARED], 
        ]);

        // Players
        $players = array_map(fn($row) => $row[GameSnapshotKey::USER_ID], $row_players);

        // Figures
        $figures = array_map(function ($row) {
            return new FigureState(
                figure_index: (int) $row[FigureStateKey::FIGURE_INDEX], 
                player_id: $row[GameSnapshotKey::USER_ID], 
                position: (int) $row[FigureStateKey::POSITION], 
                area: FigureArea::from($row[FigureStateKey::AREA]),
            );
        }, $row_figures);

        // GameState
        $state = new GameState(
            players: $players, 
            figures: $figures, 
            current_player_index: (int) ($row_state[GameStateKey::CURRENT_PLAYER_INDEX] ?? 0)
        );

        return new Game(
            id: $row_game[GameSnapshotKey::ID], 
            created_by_user_id: $row_game[GameSnapshotKey::CREATED_BY_USER_ID], 
            status: GameStatus::from($row_game[GameSnapshotKey::STATUS]), 
            rules: $rules, 
            state: $state, 
        );
    }
}