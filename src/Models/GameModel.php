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
    private bool $is_private;
    private bool $is_locked;
    private string $created_at;
    private string $updated_at;

    // Variables for specific read operations
    private string $created_by_user_name;

    // Models for write operations
    private GameRuleSetModel $rule_set_model;
    private GameStateModel $state_model;
    private array $player_array;    // max 4
    private array $figure_array;    // max 16

    public function __construct() {
        parent::__construct();

        $this->rule_set_model = new GameRuleSetModel();
        $this->state_model = new GameStateModel();
        $this->player_array = []; 
        $this->figure_array = [];
    }

    // Games - Retrieve all games
    public static function all(): array {
        $rows = static::fetchAll("SELECT * FROM games ORDER BY created_at DESC");
        return array_map(fn($row) => self::fromArray($row), $rows);
    }

    // Games - Find game by id
    public static function findById(string $game_id): ?self {
        $row = static::fetchOne(
            "SELECT 
                g.id, 
                g.name, 
                g.created_by_user_id, 
                u.username, 
                g.status, 
                g.is_private, 
                g.is_locked, 
                g.created_at, 
                g.updated_at
            FROM games g
            JOIN users u
                ON g.created_by_user_id = u.id
            WHERE g.id = :game_id 
            LIMIT 1",
            ['game_id' => $game_id]
        );

        //var_dump($row);
        //var_dump(self::fromArray($row));
        //exit;
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

    // Games - Update game status
    public function updateStatus($status): void {
        static::execute(
            sprintf(
                "UPDATE 
                    %s,
                SET
                    %s = :status
                WHERE
                    %s = :game_id",

                Application::TABLE_GAMES, 
                Application::STATUS, 
                Application::ID
            ),
            [
                'status' => $status,
                'game_id' => $this->id
            ]
        );
    }

    // Game - Update game status helper
    public function cancelGame(): void {
        $this->updateStatus(Application::STATUS_CANCELLED);
    }

    // Games - Delete game
    public function delete(): bool {
        try {
            $this->db->beginTransaction();

            //$this->figure_model->removeAllFigures($this->id);
            //$this->player_model->removeAllPlayer($this->id);
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
                Application::IS_PRIVATE, 
                Application::IS_LOCKED, 
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

    // Helper - Convert db rows to GameModel dynamically
    private static function fromArrayDynamic(array $row): self {
        $game = new self();

        foreach ($row as $key => $value) {
            $game->{$key} = $value; 
        }
        return $game;
    }

    // Helper - Convert db rows to GameModel strict
    private static function fromArray(array $row): self {
        $game = new self();
        $game->id = $row['id'];
        $game->name = $row['name'] ?? null; 
        $game->created_by_user_id = $row['created_by_user_id'] ?? null;
        $game->status = $row['status'] ?? null;
        $game->is_private = $row['is_private'] ?? null;
        $game->is_locked = $row['is_locked'] ?? null;

        if (array_key_exists(Application::USERNAME, $row)) $game->created_by_user_name = $row['username'];
        if (array_key_exists(Application::PLAYER_COUNT, $row)) $game->player_count = (int) $row[Application::PLAYER_COUNT];

        $game->rule_set_model = GameRuleSetModel::fromArray($row) ?? null;

        $game->created_at = $row['created_at'];
        $game->updated_at = $row['updated_at'];
        return $game;
    }

    // Helper
    // Helper - Status is waiting
    public function isWaiting() : bool {
        return $this->status === Application::STATUS_WAITING;
    }

    // Helper - Status is running
    public function isRunning() : bool {
        return $this->status === Application::STATUS_RUNNING;
    }

    // Helper - Status is finished
    public function isFinished() : bool {
        return $this->status === Application::STATUS_FINISHED;
    }

    // Helper - Status is cancelled
    public function isCancelled() : bool {
        return $this->status === Application::STATUS_CANCELLED;
    }

    // Helper - Is private
    public function isPrivate() : bool {
        return $this->is_private;
    }

    // Helper - Is locked
    public function isLocked() : bool {
        return $this->is_locked;
    }

    // Helper - Check for available player slots
    public function isFull(): bool {
        return $this->getPlayerCount() >= $this->getPlayerMax();
    }

    // Helper - Check if given user is already a player of the game
    public function isParticipant(UserModel $user) {
        // ToDo: Implement
        $user_id = $user->getId();
        for ($i = 0; $i < count($this->player_array); $i++) {
            $player = $this->player_array[$i];
            if ($user_id === $player->getPlayerId) {
                return true;
            }
        }
    }

    // Getter
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
    public function getRuleSetModel(): GameRuleSetModel {
        return $this->rule_set_model;
    }

    // Get the value of state_model
    public function getStateModel(): GameStateModel {
        return $this->state_model;
    }

    // Get array of all player in the game
    public function getAllPlayer(): array {
        return $this->player_array;
    }
    
    // Get player given by player id
    public function getPlayerById(string $player_id): GameStatePlayerModel {
        // ToDo: Implement this
        return new GameStatePlayerModel();
    }

    // Get array of all figures in the game
    public function getAllFigures(): array {
        return $this->figure_array;
    }

    // Get Figure given by figure id
    public function getFigureById(string $figure_id): GameStateFigureModel {
        // ToDo: Implement
        return new GameStateFigureModel();
    }
}