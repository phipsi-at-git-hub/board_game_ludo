<?php
// GameModel.php
namespace App\Models;

use Exception;

final class GameModel extends BaseModel {
    private string $id;
    private string $name;
    private string $status;
    private string $created_at;
    private string $updated_at;

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
    public function create(string $user_id, array $rules): ?string {
        $game_id = self::generateUUID();

        try {
            $this->db->beginTransaction();

            // Insert game
            $this->execute(
                "INSERT INTO games
                (id, created_by_user_id, status, created_at, updated_at)
                VALUES
                (:game_id, :user_id, 'waiting', NOW(), NOW())",
                [
                    'game_id' => $game_id,
                    'user_id' => $user_id
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
    public function update(string $name, string $status): void {
        static::execute(
            "UPDATE games SET name = :name, status = :status WHERE id = :id",
            ['id' => $this->id, 'name' => $name, 'status' => $status]
        );
        $this->name = $name;
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

    // Helper - Convert db row to GameModel object
    private static function fromArray(array $row): self {
        $game = new self();
        $game->id = $row['id'];
        $game->name = $row['name'];
        $game->status = $row['status'];
        $game->created_at = $row['created_at'];
        $game->updated_at = $row['updated_at'];
        return $game;
    }

    // Get the value of id
    public function getId() {
        return $this->id;
    }

    // Get the value of name
    public function getName() {
        return $this->name;
    }

    //Get the value of status
    public function getStatus() {
        return $this->status;
    }

    // Get the value of created_at
    public function getCreated_at() {
        return $this->created_at;
    }

    // Get the value of updated_at
    public function getUpdated_at() {
        return $this->updated_at;
    }

    // Get the value of rule_set_model
    public function getRule_set_model() {
        return $this->rule_set_model;
    }

    // Get the value of state_model
    public function getState_model() {
        return $this->state_model;
    }

    // Get the value of player_model
    public function getPlayer_model() {
        return $this->player_model;
    }

    // Get the value of figure_model
    public function getFigure_model() {
        return $this->figure_model;
    }
}