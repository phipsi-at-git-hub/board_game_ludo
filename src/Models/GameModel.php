<?php
// GameModel.php
namespace App\Models;

use Exception;
use App\Constants\Application;
use DomainException;
use LogicException;
use Throwable;

final class GameModel extends BaseModel {
    private const PLAYERS_MAX = 4;
    private const FIGURES_PER_PLAYER = 4;
    private const FIELD_LENGTH = 40;
    private const GOAL_LENGTH = 4;

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
        //$this->figure_array = [];
    }

    /**
     * Game Engine - Core
     */
    //  Game Engine - get available move for player and rolled dice
    private function getAvailableMoves(string $user_id, int $dice_value): array {
        $player = $this->getPlayerById($user_id);

        $moves = [];

        foreach ($player->getAllFigures() as $figure) {
            // ToDo: Implement
        }
        return [];
    }

    // Game Engine - Check if figure can move
    // This is the MAIN METHOD for GAME ENGINE LOGIC
    private function canMoveFigure(GameStatePlayerModel $player, GameStateFigureModel $figure, int $dice_value): bool {
        $area = $figure->getArea();

        /**
         * 1. Step - Figure is in goal area already
         */
        if ($area === Application::AREA_GOAL && $figure->getPosition() === self::GOAL_LENGTH) {
            return false;
        }

        /**
         * 2. Step - Figure is still in home area
         */

        if ($area === Application::AREA_HOME) {
            // Figure can only leave home area with a six
            if ($dice_value !== 6) {
                return false;
            }

            // Perhaps the start field of the player must be cleared
            if ($this->rule_set_model->getStartFieldMustBeCleared()) {
                if ($this->isStartFieldBlocked($player)) {
                    return false;
                }
            }
            return true;
        }

        /**
         * 3. Step - Figure is on the field / board
         */
        if ($area === Application::AREA_FIELD) {
            $relative_position = $figure->getPosition();
            $new_relative_position = $relative_position + $dice_value;

            /**
             * 3.1 Step - Is entering the goal area possible?
             */
            if ($new_relative_position >= self::FIELD_LENGTH) {
                $steps_into_goal = $new_relative_position - self::FIELD_LENGTH;

                // $dive_roll too high
                if ($steps_into_goal >= self::GOAL_LENGTH) {
                    // check for other movable figures
                    if ($this->playerHasOtherMovableFigure($player, $figure, $dice_value)) {
                        return false;
                    }

                    // no other figure is allowed to move
                    return true;
                }

                // strict goal order active?
                if ($this->isGoalPositionBlockedByStrictOrder($player, $steps_into_goal)) {
                    return false;
                }

                return true;
            }

            /**
             * 3.2 Step - Common move on field / board
             */
            $absolute_target = ($player->getStartOffset() + $new_relative_position) % self::FIELD_LENGTH;

            // own figure blocked?
            if (!$this->rule_set_model->getAllowStackOwnFigures()) {
                if ($this->isOwnFigureOnAbsolutePosition($player, $absolute_target)) {
                    return false;
                }
            }

            return true;
        }

        /**
         * 4. Step - Figure is in goal area already
         */
        if ($area === Application::AREA_GOAL) {
            $current_goal_position = $figure->getPosition();
            $new_goal_position = $current_goal_position + $dice_value;

            // too far for finish in goal area
            if ($new_goal_position >= self::GOAL_LENGTH) {
                // Check for other playable figures of player
                if ($this->playerHasOtherMovableFigure($player, $figure, $dice_value)) {
                    return false;
                }
                return true;
            }

            // check strict goal order
            if ($this->isGoalPositionBlockedByStrictOrder($player, $new_goal_position)) {
                return false;
            }
            return true;
        }

        return false;
    }

    // Game Engine - get absolute position on the field of given figure
    private function getAbsoluteFieldPosition(GameStatePlayerModel $player, GameStateFigureModel $figure): int {
        if ($figure->getArea() !== Application::AREA_FIELD) {
            throw new LogicException('Figure is not on field');
        }

        $start_offset = $player->getStartOffset(); // 0, 10, 20, 30
        $relative_position = $figure->getPosition();

        $absolute_position = ($start_offset + $relative_position) % self::FIELD_LENGTH;

        return $absolute_position;
    }

    // Game Engine - Check if
    private function isStartFieldBlocked(GameStatePlayerModel $player): bool {
        // ToDo: check logic here. Start field must be cleared by own figures. Other players figures can be removed by player itself :) 
        $start_offset = $player->getStartOffset();

        foreach ($this->player_array as $other_player) {
            foreach ($other_player->getAllFigures() as $figure) {
                if ($figure->getArea() !== Application::AREA_FIELD) {
                    continue;
                }

                $absolute_position = $this->getAbsoluteFieldPosition($other_player, $figure);

                if ($absolute_position === $start_offset) {
                    return true;
                }
            }
        }

        return false;
    }

    // Game Engine - Check if any of own figures is on position
    private function isOwnFigureOnAbsolutePosition(GameStatePlayerModel $player, int $absolute_position): bool {
        foreach ($player->getAllFigures() as $figure) {
            if ($figure->getArea() !== Application::AREA_FIELD) {
                continue;
            }

            $figure_absolute_position = $this->getAbsoluteFieldPosition($player, $figure);

            if ($figure_absolute_position === $absolute_position) {
                return true;
            }
        }
        return false;
    }

    private function getEnemyFiguresOnAbsolutePosition(GameStatePlayerModel $current_player, int $absolute_position): ?array {
        foreach ($this->player_array as $other_player) {
            if ($other_player->getUserId() === $current_player->getUserId()) {
                continue;
            }

            foreach ($other_player->getAllFigures() as $figure) {
                if ($figure->getArea() !== Application::AREA_FIELD) {
                    continue;
                }

                $figure_absolute_position = $this->getAbsoluteFieldPosition($other_player, $figure);

                if ($figure_absolute_position === $absolute_position) {
                    return [
                        'player' => $other_player, 
                        'figure' => $figure
                    ];
                }
            }
        }
        return null;
    }

    // Game Engine - Check if figure can enter goal area
    private function canEnterGoal(GameStatePlayerModel $player, GameStateFigureModel $figure, int $dice_value): ?int {
        if ($figure->getArea() !== Application::AREA_FIELD) {
            return null;
        }

        $relative_position = $figure->getPosition();
        $new_relative_position = $relative_position + $dice_value;

        if ($new_relative_position < self::FIELD_LENGTH) {
            return null; // not yet reached goal
        }

        $steps_into_goal = $new_relative_position - self::FIELD_LENGTH;

        if ($steps_into_goal >= self::GOAL_LENGTH) {
            return null; // to many steps to fit in goal area
        }

        return $steps_into_goal; // fit into goal area
    }

    // Game Engine - Check if figure can move through goal area to finish
    private function isGoalPositionBlockedByStrictOrder(GameStatePlayerModel $player, int $target_goal_position): bool {
        if (!$this->rule_set_model->getStrictGoalOrder()) {
            return false;
        }

        foreach ($player->getAllFigures() as $figure) {
            if ($figure->getArea() !== Application::AREA_GOAL) {
                continue;
            }

            if ($figure->getPosition() < $target_goal_position) {
                return true;
            }
        }

        return false;
    }

    private function playerHasOtherMovableFigure(GameStatePlayerModel $player, GameStateFigureModel $excluded_figure, int $dice_value): bool {
        foreach ($player->getAllFigures() as $figure) {
            if ($figure === $excluded_figure) {
                continue;
            }

            if ($this->canMoveFigure($player, $figure, $dice_value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Database
     */
    // Database - Retrieve all games
    public static function all(): array {
        $rows = static::fetchAll("SELECT * FROM games ORDER BY created_at DESC");
        return array_map(fn($row) => self::fromArray($row), $rows);
    }

    // Get all games
    public static function getAllGames(): array {
        $rows = static::fetchAll(
            sprintf(
                "SELECT
                    g.*, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    COUNT(p.%s) as player_count
                FROM %s g 
                LEFT JOIN %s r 
                    ON g.%s = r.%s
                LEFT JOIN %s p
                    ON g.%s = p.%s
                GROUP BY g.%s 
                ORDER BY g.%s DESC", 

                Application::ALLOW_BOTS, 
                Application::EXTRA_ROLL_LIMIT, 
                Application::ALLOW_STACK_OWN_FIGURES, 
                Application::STRICT_GOAL_ORDER, 
                Application::START_FIELD_MUST_BE_CLEARED, 

                Application::USER_ID, 

                Application::TABLE_GAMES, 

                Application::TABLE_RULES, 
                Application::ID, 
                Application::GAME_ID, 

                Application::TABLE_PLAYERS, 
                Application::ID, 
                Application::GAME_ID, 

                Application::ID, 
                Application::CREATED_AT
            )
        );
        return array_map(fn($row) => self::fromArray($row), $rows);
    }

    // Database - Get all open game available to join
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

    // Database - Find game by id
    public static function findById(string $game_id): ?self {
        $row = static::fetchOne(
            sprintf(
                "SELECT 
                    g.*, 
                    u.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s 
                FROM %s g
                JOIN %s u
                    ON g.%s = u.%s 
                LEFT JOIN %s r
                    ON g.%s = r.%s
                WHERE g.%s = :game_id 
                LIMIT 1", 

                Application::USERNAME, 
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

                Application::ID
            ), 
            ['game_id' => $game_id]
        );

        if (!$row) {
            return null;
        }

        $game = self::fromArray($row);

        // Load all players of the game
        $players = static::fetchAll(
            sprintf(
                "SELECT
                    p.%s, 
                    p.%s, 
                    u.%s, 
                    p.%s, 
                    p.%s 
                FROM %s p
                JOIN %s u
                    ON p.%s = u.%s
                WHERE p.%s = :game_id
                ORDER BY p.%s ASC", 

                Application::GAME_ID, 
                Application::USER_ID, 
                Application::USERNAME, 
                Application::CREATED_AT, 
                Application::UPDATED_AT, 

                Application::TABLE_PLAYERS, 

                Application::TABLE_USERS, 
                Application::USER_ID, 
                Application::ID, 

                Application::GAME_ID, 

                Application::CREATED_AT
            ), 
            ['game_id' => $game_id]
        );

        $game->player_array = array_map(fn($row) => GameStatePlayerModel::fromArray($row), $players);

        // Database - Load all figures of the game
        $figures = static::fetchAll(
            sprintf(
                "SELECT
                    f.%s, 
                    f.%s, 
                    f.%s, 
                    f.%s, 
                    f.%s, 
                    f.%s, 
                    f.%s 
                FROM %s f
                WHERE f.%s = :game_id", 

                Application::GAME_ID, 
                Application::USER_ID, 
                Application::FIGURE_INDEX, 
                Application::POSITION, 
                Application::AREA, 
                Application::CREATED_AT, 
                Application::UPDATED_AT,

                Application::TABLE_FIGURES, 
                Application::GAME_ID
            ),
            ['game_id' => $game_id]
        );

        /*
        foreach ($game->player_array as $player) {
            foreach ($figures as $figure) {
                $figure = GameStateFigureModel::fromArray($figure);
                if ($figure && $figure->getUserId() === $player->getUserId()) {
                    $player->addFigure($figure);
                }
            }
        }
        */
        $players_by_user_id = [];
        foreach ($game->player_array as $player) {
            $players_by_user_id[$player->getUserId()] = $player;
        }

        foreach ($figures as $row) {
            $figure = GameStateFigureModel::fromArray($row);
            $user_id = $figure->getUserId();

            if (isset($players_by_user_id[$user_id])) {
                $players_by_user_id[$user_id]->addFigure($figure);
            }
        }
        
        return $game;
    }

    // Database - Find game by name
    public static function findByName(string $game_name): ?self {
        $row = static::fetchAll(
            "SELECT * FROM games WHERE name = :name LIMIT 1",
            ['name' => $game_name]
        );
        return $row ? self::fromArray($row) : null;
    }

    // Database - Count all games
    public static function countAll() : int {
        return static::count("SELECT COUNT(*) FROM games");
    }

    // Database - Count all games with specific status
    public static function countByStatus(string $status): int {
        return static::count(
            "SELECT COUNT(*) FROM games WHERE status = :status",
            ['status' => $status]
        );
    }

    // Database - Create new game
    public function create(string $user_id, string $game_name, array $game_options, array $rules): ?string {
        $game_id = self::generateUUID();

        try {
            $this->db->beginTransaction();

            // Insert game
            $this->execute(
                sprintf(
                    "INSERT INTO %s (%s, %s, %s, %s, %s, %s, created_at, updated_at)
                    VALUES (:id, :name, :created_by_user_id, :status, :is_private, :is_locked, NOW(), NOW())",
                    Application::TABLE_GAMES,
                    Application::ID,
                    Application::NAME, 
                    Application::CREATED_BY_USER_ID,
                    Application::STATUS, 
                    Application::IS_PRIVATE, 
                    Application::IS_LOCKED
                ),
                [
                    'id' => $game_id,
                    'name' => $game_name, 
                    'created_by_user_id' => $user_id,
                    'status' => Application::STATUS_WAITING, 
                    'is_private' => $game_options[Application::IS_PRIVATE], 
                    'is_locked' => $game_options[Application::IS_LOCKED]
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

    // Database - Update game 
    public function update(string $game_name, array $game_data, array $rule_set): bool {
        try {
            $this->db->beginTransaction();

            $this->updateGameData($game_name, $game_data);
            $this->rule_set_model->update($this->id, $rule_set);

            $this->db->commit();

            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
            return false;
        }
    }

    // Database - Update game data
    public function updateGameData(string $game_name, array $game_options): void {
        $this->db->execute(
            sprintf(
                "UPDATE games 
                SET
                    name = :name, 
                    is_private = :is_private, 
                    is_locked = :is_locked 
                WHERE id = :id"
            ), [
                'id' => $this->id, 
                'name' => $game_name, 
                'is_private' => $game_options['is_private'], 
                'is_locked' => $game_options['is_locked']
            ]
        );
    }

    // Database - Update game status
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

    // Database - Delete game
    public function delete(): bool {
        try {
            $this->db->beginTransaction();

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

    /** 
     * Game 
     * */
    // Game - Update game status helper
    public function cancelGame(): void {
        $this->updateStatus(Application::STATUS_CANCELLED);
    }

    // Game - Join game - player 
    public function join(string $user_id): bool {
        if ($this->status !== Application::STATUS_WAITING) {
            throw new DomainException('Game cannot be joined');
        }

        if ($this->hasPlayer($user_id)) {
            throw new DomainException('User already joined');
        }

        //echo $this->getPlayerCount();exit;
        if ($this->getPlayerCount() >= $this->getPlayerMax()) {
            throw new DomainException('Game is full');
        }

        // ToDo: add Player
        try {
            $this->db->beginTransaction();

            GameStatePlayerModel::addPlayer($this->getId(), $user_id);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Game - Leave game - player
    public function leave(string $user_id): bool {
        if ($this->status === Application::STATUS_RUNNING) {
            throw new DomainException('Game leave now');
        }

        if (!$this->hasPlayer($user_id)) {
            throw new DomainException('User not in the game');
        }

        try {
            $this->db->beginTransaction();

            GameStateFigureModel::removeAllUserFigures($this->getId(), $user_id);
            GameStatePlayerModel::removePlayer($this->getId(), $user_id);
            
            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Helper
     */
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

        if (array_key_exists(Application::USERNAME, $row)) $game->created_by_user_name = $row[Application::USERNAME];
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
            if ($user_id === $player->getUserId()) {
                return true;
            }
        }
        return false;
    }

    // Helper - Check if user is already player in game
    public function hasPlayer(string $user_id): bool {
        foreach ($this->player_array as $player) {
            if ($player->getUserId() === $user_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Getter
     */
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
        if (isset($this->player_count)) {
            return $this->player_count;
        }
        return count($this->player_array);
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
        return GameStatePlayerModel::getPlayerById($this->id, $player_id);
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

    // Get figures for player
    public function getFiguresGroupedByPlayer(): array {
        $grouped = [];

        foreach ($this->figure_array as $figure) {
            $grouped[$figure->getUserId()][] = $figure;
        }

        return $grouped;
    }
}