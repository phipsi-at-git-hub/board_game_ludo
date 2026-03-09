<?php 
// src/Models/GameStateModel.php
namespace App\Models;

use App\Constants\Application;

final class GameStateModel extends BaseModel {
    // ToDo: Use constant from application.php 
    private int $current_player_index;
    private ?int $current_dice_roll;
    private int $leave_home_attempts_used;
    private int $extra_rolls_on_six_used;
    private string $winner_user_id;
    private string $created_at;
    private string $updated_at;

    // Find game state by game id
    public static function findByGameId(string $game_id): ?array {
        return static::fetchOne(
            "SELECT * FROM game_state WHERE game_id = :gid LIMIT 1",
            ['gid' => $game_id]
        );
    }

    // Create new game state for given game
    public static function create(string $game_id): bool {
        return static::execute(
            "INSERT INTO game_state
            (game_id, current_player_index, current_dice_roll, leave_home_attempts_used, created_at, updated_at)
            VALUES
            (:game_id, 0, NULL, 0, NOW(), NOW())",
            ['game_id' => $game_id]
        );
    }

    // Update game state for given game
    public static function updateCurrentPlayer (): void {}

    // Delete game state for given game
    public static function delete(string $game_id): bool {
        return static::execute(
            "DELETE FROM game_state WHERE game_id = :game_id",
            ['game_id' => $game_id]
        );
    }

    /**
     * Helper
     */
    // Helper - Convert db row to GameModel object
    public static function fromArray(array $row): self {
        $game_state = new self();

        if (array_key_exists(Application::CURRENT_PLAYER_INDEX, $row)) $game_state->current_player_index = (int) $row[Application::CURRENT_PLAYER_INDEX];
        if (array_key_exists(Application::CURRENT_DICE_ROLL, $row)) $game_state->current_dice_roll = (int) $row[Application::CURRENT_DICE_ROLL];
        if (array_key_exists(Application::LEAVE_HOME_ATTEMPTS_USED, $row)) $game_state->leave_home_attempts_used = (int) $row[Application::LEAVE_HOME_ATTEMPTS_USED];
        if (array_key_exists(Application::EXTRA_ROLLS_ON_SIX_USED, $row)) $game_state->extra_rolls_on_six_used = (int) $row[Application::EXTRA_ROLLS_ON_SIX_USED];
        if (array_key_exists(Application::WINNER_USER_ID, $row)) $game_state->winner_user_id = (string) $row[Application::WINNER_USER_ID];

        $game_state->created_at = $row['created_at'];
        $game_state->updated_at = $row['updated_at'];
        return $game_state;
    }

    /**
     * Getter
     */
    // Get current player index
    public function getCurrentPlayerIndex(): int {
        return $this->current_player_index;
    }

    // Get current dice roll
    public function getCurrentDiceRoll(): int {
         return $this->current_dice_roll;
    }

    // Get leave home attempts used
    public function getLeaveHomeAttemptsUsed(): int {
        return $this->leave_home_attempts_used;
    }

    // Get extra rolls on six used
    public function getExtraRollsOnSixUsed(): int {
        return $this->extra_rolls_on_six_used;
    }

    // Get winner user id
    public function getWinnerUserId(): string {
        return $this->winner_user_id;
    }

    /**
     * Setter
     */
    // Set current dice roll
    public function setCurrentDiceRoll(?int $dice_value): void {
        $this->current_dice_roll = $dice_value;
    }

    // Set leave home attempts used
    private function setLeaveHomeAttemptsUsed(int $attempts): void {
        $this->leave_home_attempts_used = $attempts;
    }

    // Reset leave home attempts used
    public function resetLeaveHomeAttemptsUsed(): void {
        $this->leave_home_attempts_used = 0;
    }

    // Increase leave home attempts used by 1
    public function incrementLeaveHomeAttemptsUsed(): void {
        $this->leave_home_attempts_used++;
    }

    // Set extra rolls on six used
    private function setExtraRollsOnSixUsed(int $rolls_used): void {
        $this->extra_rolls_on_six_used = $rolls_used;
    }

    // Reset extra rolls on six used
    public function resetExtraRollsOnSixUsed(): void {
        $this->extra_rolls_on_six_used = 0;
    }

    // Increase extra rolls on six used by 1
    public function incrementExtraRollsOnSixUsed(): void {
        $this->extra_rolls_on_six_used++;
    }

    // Set current player index
    public function setCurrentPlayerIndex(int $player_index): void {
        $this->current_player_index = $player_index;
    }
}