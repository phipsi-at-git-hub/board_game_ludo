<?php 
// src/Models/Game/State/GameStateModel.php
namespace App\Models\Game\State;

use App\Constants\Application;
use App\Models\BaseModel;

final class GameStateModel extends BaseModel {
    // ToDo: Use constant from application.php 
    private string $game_id;
    private int $current_player_index;
    private ?int $current_dice_roll;
    private int $current_turn_counter;
    private int $leave_home_attempts_used;
    private int $extra_rolls_on_six_used;
    private ?string $winner_user_id;
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
            (game_id, current_player_index, current_dice_roll, current_turn_counter, leave_home_attempts_used, created_at, updated_at)
            VALUES
            (:game_id, 0, NULL, 0, 0, NOW(), NOW())",
            ['game_id' => $game_id]
        );
    }

    // Store current_player_index
    public function saveCurrentPlayerIndex(int $player_index): bool {
        $game_state_array = $this->toArray();
        $game_state_array[Application::CURRENT_PLAYER_INDEX] = $player_index;

        return $this->updateCurrentState($this->game_id, $game_state_array);
    }

    // Store current_dice_roll
    public function saveCurrentDiceRoll(?int $dice_value): bool {
        $game_state_array = $this->toArray();
        $game_state_array[Application::CURRENT_DICE_ROLL] = $dice_value;

        return $this->updateCurrentState($this->game_id, $game_state_array);
    }

    // Store leave_home_attempts_used
    public function saveLeaveHomeAttemptsUsed(int $leave_home_attempts_used): bool {
        $game_state_array = $this->toArray();
        $game_state_array[Application::LEAVE_HOME_ATTEMPTS_USED] = $leave_home_attempts_used;

        return $this->updateCurrentState($this->game_id, $game_state_array);
    }

    // Store extra_rolls_on_six_used
    public function saveExtraRollsOnSixUsed(int $extra_rolls_on_six_used): bool {
        $game_state_array = $this->toArray();
        $game_state_array[Application::EXTRA_ROLLS_ON_SIX_USED] = $extra_rolls_on_six_used;

        return $this->updateCurrentState($this->game_id, $game_state_array);
    }

    // Store leave_home_attempts_used
    public function saveWinnerUserId(?string $winner_user_id): bool {
        $game_state_array = $this->toArray();
        $game_state_array[Application::WINNER_USER_ID] = $winner_user_id;

        return $this->updateCurrentState($this->game_id, $game_state_array);
    }

    // Save current game state
    public function save(): bool {
        return $this->updateCurrentState($this->game_id, $this->toArray());
    }

    // Update game state for given game
    private static function updateCurrentState (string $game_id, array $state_array): bool {
        return static::execute(
            sprintf(
                "UPDATE 
                    game_state 
                SET 
                    current_player_index = :current_player_index, 
                    current_dice_roll = :current_dice_roll, 
                    current_turn_counter = :current_turn_counter, 
                    leave_home_attempts_used = :leave_home_attempts_used, 
                    extra_rolls_on_six_used = :extra_rolls_on_six_used, 
                    winner_user_id = :winner_user_id
                WHERE 
                    game_id = :game_id", 

                Application::TABLE_STATE, 

                Application::CURRENT_PLAYER_INDEX, 
                Application::CURRENT_DICE_ROLL, 
                Application::CURRENT_TURN_COUNTER, 
                Application::LEAVE_HOME_ATTEMPTS_USED, 
                Application::EXTRA_ROLLS_ON_SIX_USED, 
                Application::WINNER_USER_ID
                ), [
                    'current_player_index' => $state_array[Application::CURRENT_PLAYER_INDEX], 
                    'current_dice_roll' => $state_array[Application::CURRENT_DICE_ROLL], 
                    'current_turn_counter' => $state_array[Application::CURRENT_TURN_COUNTER], 
                    'leave_home_attempts_used' => $state_array[Application::LEAVE_HOME_ATTEMPTS_USED], 
                    'extra_rolls_on_six_used' => $state_array[Application::EXTRA_ROLLS_ON_SIX_USED], 
                    'winner_user_id' => $state_array[Application::WINNER_USER_ID], 
                    'game_id' => $game_id
                ]
        );
        return false;
    }

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
    // Helper - Convert db row to GameStateModel object
    public static function fromArray(array $row): self {
        $game_state = new self();
        $game_state->game_id = $row[Application::GAME_ID];

        $game_state->current_player_index = self::hydrateInt($row, Application::CURRENT_PLAYER_INDEX);
        $game_state->current_dice_roll = self::hydrateIntOrNull($row, Application::CURRENT_DICE_ROLL);
        $game_state->current_turn_counter = self::hydrateInt($row, Application::CURRENT_TURN_COUNTER); 
        $game_state->leave_home_attempts_used = self::hydrateInt($row, Application::LEAVE_HOME_ATTEMPTS_USED);
        $game_state->extra_rolls_on_six_used = self::hydrateInt($row, Application::EXTRA_ROLLS_ON_SIX_USED);
        $game_state->winner_user_id = self::hydrateStringOrNull($row, Application::WINNER_USER_ID);

        $game_state->created_at = $row['created_at'];
        $game_state->updated_at = $row['updated_at'];
        return $game_state;
    }

    // Helper - Create Array from GameStateModel
    private function toArray(): array {
        $game_state_array[Application::GAME_ID] = $this->game_id;
        $game_state_array[Application::CURRENT_PLAYER_INDEX] = $this->current_player_index;
        $game_state_array[Application::CURRENT_DICE_ROLL] = $this->current_dice_roll;
        $game_state_array[Application::CURRENT_TURN_COUNTER] = $this->current_turn_counter; 
        $game_state_array[Application::LEAVE_HOME_ATTEMPTS_USED] = $this->leave_home_attempts_used;
        $game_state_array[Application::EXTRA_ROLLS_ON_SIX_USED] = $this->extra_rolls_on_six_used;
        $game_state_array[Application::WINNER_USER_ID] = $this->winner_user_id;

        return $game_state_array;
    }

    // Helper - Reset GameState
    public function reset() {
        $this->setCurrentPlayerIndex(0);
        $this->resetCurrentDiceRoll();
        $this->resetCurrentTurnCounter();
        $this->resetLeaveHomeAttemptsUsed();
        $this->resetExtraRollsOnSixUsed();
        $this->setWinnerUserId(null);
        $this->save();
    }

    /**
     * Getter
     */
    // Get current player index
    public function getCurrentPlayerIndex(): int {
        return $this->current_player_index;
    }

    // Get current dice roll
    public function getCurrentDiceRoll(): ?int {
         return $this->current_dice_roll;
    }

    // Get current turn counter
    public function getCurrentTurnCounter(): int {
        return $this->current_turn_counter; 
    }

    // Get leave home attempts used
    public function getLeaveHomeAttemptsUsed(): ?int {
        return $this->leave_home_attempts_used;
    }

    // Get extra rolls on six used
    public function getExtraRollsOnSixUsed(): ?int {
        return $this->extra_rolls_on_six_used;
    }

    // Get winner user id
    public function getWinnerUserId(): ?string {
        return $this->winner_user_id;
    }

    /**
     * Setter
     */
    // Set current dice roll
    public function setCurrentDiceRoll(?int $dice_value): void {
        $this->current_dice_roll = $dice_value;
    }

    // Reset current dice roll
    public function resetCurrentDiceRoll(): void {
        $this->setCurrentDiceRoll(null);
    }

    // Increase current turn counter by 1
    public function incrementCurrentTurnCounter(): void {
        $this->current_turn_counter++;
    }

    // Reset current turn counter
    public function resetCurrentTurnCounter(): void {
        $this->current_turn_counter = 0;
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

    // Set winner user id
    public function setWinnerUserId(?string $user_id): void {
        $this->winner_user_id = $user_id;
    }
}