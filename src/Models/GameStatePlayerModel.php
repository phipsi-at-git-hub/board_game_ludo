<?php 
// src/Models/GameStatePlayerModel.php
namespace App\Models;

use App\Constants\Application;

final class GameStatePlayerModel extends BaseModel {
    // ToDo: Use constant from application.php 
    private string $game_id;
    private string $user_id;
    private string $user_name;
    private int $player_index;
    private string $created_at;
    private string $updated_at;

    private array $figure_array = [];    // max 4

    // Find game state players for given game
    public static function findByGameId(string $game_id): array {
        return static::fetchAll(
            sprintf("SELECT * FROM game_state_players WHERE game_id = :game_id"),
            ['game_id' => $game_id]
        );
    }

    // Add player to game and 4 figures
    public static function addPlayer(string $game_id, string $user_id, int $player_index, bool $all_start_from_home = true): bool {
        $row = static::execute(
            "INSERT INTO game_state_players
             (game_id, user_id, player_index, created_at, updated_at)
             VALUES
             (:game_id, :user_id, :player_index, NOW(), NOW())",
            [
                'game_id' => $game_id,
                'user_id' => $user_id, 
                'player_index' => $player_index
            ]
        );

        if ($row) {
            // add 4 figures
            GameStateFigureModel::createInitialFigureSet($game_id, $user_id, $all_start_from_home);
            return true;
        }
        return false;
    }

    // Find player by game_id and user_id
    public static function getPlayerById(string $game_id, string $user_id): self {
        $row = self::fetchOne(
            sprintf("
                SELECT s.*, u.username  
                FROM game_state_players s 
                JOIN users u
                    ON s.user_id = u.id 
                WHERE 
                    s.game_id = :game_id AND  
                    s.user_id = :user_id "), 
            [
                'game_id' => $game_id, 
                'user_id' => $user_id
            ]
        );

        $player = self::fromArray($row);
        $player->addSetOfFigures(GameStateFigureModel::findByGameIdAndPlayerId($game_id, $user_id));

        return $player;
    }

    // get player by player index
    public static function getPlayerByPlayerIndex(string $game_id, int $player_index): self {
        $row = self::fetchOne(
            sprintf("
                SELECT s.*, u.username  
                FROM game_state_players s 
                JOIN users u
                    ON s.user_id = u.id 
                WHERE 
                    s.game_id = :game_id AND 
                    s.player_index = :player_index "), 
            [
                'game_id' => $game_id, 
                'player_index' => $player_index
            ]
        );

        $player = self::fromArray($row);
        $player->addSetOfFigures(GameStateFigureModel::findByGameIdAndPlayerId($game_id, $player->getUserId()));

        return $player;
    }

    // Remove given player from game
    public static function removePlayer(string $game_id, string $user_id): bool {
        return static::execute(
            "DELETE FROM game_state_players WHERE game_id = :game_id AND user_id = :user_id",
            ['game_id' => $game_id, 'user_id' => $user_id], 

        );
    }

    // Remove all player from game
    public static function removeAllPlayer(string $game_id): bool {
        return static::execute(
            "DELETE FROM game_state_players WHERE game_id = :game_id",
            ['game_id' => $game_id]
        );
    }

    /**
     * Getter
     */
    // Getter - get user id
    public function getUserId(): string {
        return $this->user_id;
    }

    // Getter - get username
    public function getUsername(): string {
        return $this->user_name;
    }

    // Getter - get player index
    public function getPlayerIndex() {
        return $this->player_index;
    }

    // Getter - Get created at
    public function getCreatedAt(): string {
        return $this->created_at;
    }

    // Getter - Get updated at
    public function getUpdatedAt(): string {
        return $this->updated_at;
    }

    // Getter - Get figures
    public function getAllFigures(): array {
        return $this->figure_array;
    }

    // Getter - Get figure by figure index
    public function getFigureByFigureIndex(string $game_id, string $user_id, int $figure_index): GameStateFigureModel {
        return GameStateFigureModel::findByFigureIndex($game_id, $user_id, $figure_index);
    }

    // Getter - Get start offset of players figures
    public function getStartOffset(): int {$offset = 0;
        // Should be part of GameModel since field length is property of game not the play -> ( $field_length / $player_max ) * $player->getPlayerIndex();
        return $this->player_index * 10;
    }

    // Getter - Get figure of player by figure id
    public function getFigureById(string $figure_id) {}

    /**
     * Setter
     */
    // Setter - Add single Figure
    public function addFigure(GameStateFigureModel $figure): void {
        $this->figure_array[] = $figure; 
    }

    // Setter - Add array of figures
    public function addSetOfFigures(array $figure_set): void {
        $this->figure_array = $figure_set;
    }

    /**
     * Helper
     */
    // Helper - Convert db rows to GameModel dynamically
    private static function fromArrayDynamic(array $row): self {
        $game_state_player = new self();

        foreach ($row as $key => $value) {
            $game_state_player->{$key} = $value; 
        }
        return $game_state_player;
    }

    // Helper - Convert db rows to GameStatePlayerModel strict
    public static function fromArray(array $row) : self {
        $game_state_player = new self();

        $game_state_player->game_id = $row[Application::GAME_ID];
        $game_state_player->user_id = $row[Application::USER_ID];
        if (array_key_exists(Application::USERNAME, $row))  $game_state_player->user_name = $row[Application::USERNAME];
        $game_state_player->player_index = $row[Application::PLAYER_INDEX]; 
        $game_state_player->created_at = $row[Application::CREATED_AT];
        $game_state_player->updated_at = $row[Application::UPDATED_AT];

        return $game_state_player;
    }

    // Helper - Create Array from GameStatePlayerModel
    private function toArray(): array {
        $game_state_player[Application::GAME_ID] = $this->game_id;
        $game_state_player[Application::USER_ID] = $this->user_id;
        $game_state_player[Application::PLAYER_INDEX] = $this->player_index;

        return $game_state_player;
    }
}