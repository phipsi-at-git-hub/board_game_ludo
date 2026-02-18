<?php
// GameModel.php
namespace App\Models;

use Exception;
use App\Constants\Application;

final class GameModel extends BaseModel {
    private const PLAYERS_MAX = 4;
    private string $id;
    private string $name;
    private string $created_by_user_id;
    private int $player_count;
    private string $status;
    private string $created_at;
    private string $updated_at;

    // Variables for specific read operations
    private string $created_by_user_name;

    // Models for write operations
    private GameRuleSetModel $rule_set_model;
    private GameStateModel $state_model;
    private GameStatePlayerModel $player_model;
    private GameStateFigureModel $figure_model;

    public function __construct() {
        parent::__construct();

        $this->rule_set_model = new GameRuleSetModel();
        $this->state_model = new GameStateModel();
        $this->player_model = new GameStatePlayerModel();
        $this->figure_model = new GameStateFigureModel();
    }

    // Games - Retrieve all games
    public static function all(): array {
        $rows = static::fetchAll("SELECT * FROM games ORDER BY created_at DESC");
        return array_map(fn($row) => self::fromArray($row), $rows);
    }

    // Games - Find game by id
    public static function findById(string $game_id): ?self {
        $row = static::fetchAll(
            "SELECT * FROM games WHERE id = :id LIMIT 1",
            ['id' => $game_id]
        );
        return $row ? self::fromArray($row) : null;
    }

    // Games - Find game by name
    public static function findByName(string $game_name): ?self {
        $row = static::fetchAll(
            "SELECT * FROM games WHERE name = :name LIMIT 1",
            ['name' => $game_name]
        );
        return $row ? self::fromArray($row) : null;
    }

    // Games - Count all games
    public static function countAll() : int {
        return static::count("SELECT COUNT(*) FROM games");
    }

    // Games - Count all games with specific status
    public static function countByStatus(string $status): int {
        return static::count(
            "SELECT COUNT(*) FROM games WHERE status = :status",
            ['status' => $status]
        );
    }

    // Games - Create new game
    public function create(string $user_id, string $game_name, array $rules): ?string {
        $game_id = self::generateUUID();

        try {
            $this->db->beginTransaction();

            // Insert game
            $this->execute(
                sprintf(
                    "INSERT INTO %s (%s, %s, %s, %s, created_at, updated_at)
                    VALUES (:id, :name, :created_by_user_id, :status, NOW(), NOW())",
                    Application::TABLE_GAMES,
                    Application::ID,
                    Application::NAME, 
                    Application::CREATED_BY_USER_ID,
                    Application::STATUS
                ),
                [
                    'id' => $game_id,
                    'name' => $game_name, 
                    'created_by_user_id' => $user_id,
                    'status' => Application::STATUS_WAITING
                ]
            );

            // Insert rule set
            $this->rule_set_model->create($game_id, $rules);

            // Insert state
            $this->state_model->create($game_id);

            // Commit 
            $this->db->commit();

            return $game_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Games - Update game data
    public function update(string $created_by_user_id, string $status): void {
        static::execute(
            "UPDATE games SET created_by_user_id = :created_by_user_id, status = :status WHERE id = :id",
            ['id' => $this->id, 'created_by_user_id' => $created_by_user_id, 'status' => $status]
        );
        $this->created_by_user_id = $created_by_user_id;
        $this->status = $status;
    }

    // Games - Delete game
    public function delete(): bool {
        try {
            $this->db->beginTransaction();

            $this->figure_model->removeAllFigures($this->id);
            $this->player_model->removeAllPlayer($this->id);
            $this->state_model->delete($this->id);
            $this->rule_set_model->delete($this->id);

            $this->db->execute(
                "DELETE FROM games WHERE id = :game_id",
                ['game_id' => $this->id]
            );

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
            return false;
        }
    }

    // get all open game available to join
    public static function getAllOpenGames(): array {
        $rows = static::fetchAll(
            sprintf(
                "SELECT 
                    g.%s,
                    g.%s, 
                    g.%s,
                    u.%s, 
                    g.%s, 
                    g.%s,
                    g.%s, 
                    COUNT(p.%s) AS player_count, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s
                FROM %s g
                JOIN %s u
                    ON g.%s = u.%s
                JOIN %s r
                    ON g.%s = r.%s
                LEFT JOIN %s p 
                    ON g.%s = p.%s
                WHERE g.%s = :status
                GROUP BY g.%s
                ORDER BY g.%s DESC",
                
                Application::ID,
                Application::NAME,
                Application::CREATED_BY_USER_ID,
                Application::USERNAME,
                Application::STATUS, 
                Application::CREATED_AT,
                Application::UPDATED_AT, 
                Application::USER_ID,
                Application::ALLOW_BOTS, 
                Application::EXTRA_ROLL_LIMIT, 
                Application::ALLOW_STACK_OWN_FIGURES, 
                Application::STRICT_GOAL_ORDER, 
                Application::START_FIELD_MUST_BE_CLEARED, 

                Application::TABLE_GAMES,

                Application::TABLE_USERS, 
                Application::CREATED_BY_USER_ID,
                Application::ID, 

                Application::TABLE_RULES, 
                Application::ID, 
                Application::GAME_ID, 
                
                Application::TABLE_PLAYERS,
                Application::ID,
                Application::GAME_ID,

                Application::STATUS,
                Application::ID,
                Application::CREATED_AT
            ),
            ['status' => Application::STATUS_WAITING]
        );
        return array_map(fn($row) => self::fromArray($row), $rows);
    }

    // Helper - Convert db row to GameModel object
    private static function fromArray(array $row): self {
        $game = new self();
        $game->id = $row['id'];
        $game->name = $row['name'] ?? null; 
        $game->created_by_user_id = $row['created_by_user_id'] ?? null;
        $game->created_by_user_name = $row['username'] ?? null;
        $game->status = $row['status'] ?? null;
        $game->player_count = (int) $row['player_count'] ?? null;

        $game->rule_set_model = GameRuleSetModel::fromArray($row) ?? null;

        $game->created_at = $row['created_at'];
        $game->updated_at = $row['updated_at'];
        return $game;
    }

    // Get the value of id
    public function getId() {
        return $this->id;
    }

    // Get the value of id
    public function getName() {
        return $this->name;
    }

    // Get the value of created_by_user_id
    public function getCreatedByUserId() {
        return $this->created_by_user_id;
    }

    // Get the value of created_by_user_name
    public function getCreatedByUserName() {
        return $this->created_by_user_name;
    }

    // Get number of maximum allowed player
    public function getPlayerMax(): int {
        return self::PLAYERS_MAX;
    }

    // Get the number of player of the game
    public function getPlayerCount(): int {
        return $this->player_count;
    }

    //Get the value of status
    public function getStatus() {
        return $this->status;
    }

    // Get the value of created_at
    public function getCreatedAt() {
        return $this->created_at;
    }

    // Get the value of updated_at
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    // Get Subset of Models - Future preparations 
    // Get the value of rule_set_model
    public function getRuleSetModel() {
        return $this->rule_set_model;
    }

    // Get the value of state_model
    public function getStateModel() {
        return $this->state_model;
    }

    // Get the value of player_model
    public function getPlayerModel() {
        return $this->player_model;
    }

    // Get the value of figure_model
    public function getFigureModel() {
        return $this->figure_model;
    }
}