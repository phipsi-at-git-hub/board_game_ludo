<?php
// src/Models/GameModel.php
namespace App\Models;

use App\Constants\Application;
use Exception;
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
    private bool $is_test_game;
    private string $created_at;
    private string $updated_at;

    // Variables for specific read operations
    private string $created_by_user_name;

    // Models for write operations
    private GameRuleSetModel $rule_set_model;
    private GameStateModel $state_model;
    private array $player_array;    // max 4
    // private array $figure_array;    // max 16

    public function __construct() {
        parent::__construct();

        $this->rule_set_model = new GameRuleSetModel();
        $this->state_model = new GameStateModel();
        $this->player_array = []; 
    }

    /**
     * Game Engine - CORE
     */
    /**
     * Game Engine - Apply Move 
     */
    public function applyMove(string $user_id, array $move): void {
        // Check if it's users turn 
        $this->assertPlayerTurn($user_id);
        // Check if turn counter is validate
        $this->validateTurnCounter($move[Application::DTO_GAME_TURN]);

        $current_player = $this->getCurrentPlayer();

        // 1. Game finished?
        if ($this->state_model->getWinnerUserId() !== null) {
            throw new LogicException('Game already finished');
        }

        // 3. Dice rolled already?
        $dice_value = $this->state_model->getCurrentDiceRoll();
        if ($dice_value === null) {
            throw new LogicException('No dice roll available');
        }

        // 4 Validate Move
        $available_moves = $this->getAvailableMoves($user_id, $dice_value);
        if (!$this->isMoveInList($move, $available_moves)) {
            throw new LogicException('Illegal move');
        }

        // 5. Execute Move
        $figure_index = $move[Application::DTO_FIGURE_INDEX];
        $figure = $current_player->getFigureByFigureIndex($this->getId(), $current_player->getUserId(), $figure_index);

        // Execute Kicks
        if (!empty($move[Application::DTO_IS_KICK])) {
            $kicked_player = $this->getPlayerById($move[Application::DTO_KICKED_PLAYER_ID]);
            $kicked_figure = $kicked_player->getFigureByFigureIndex($this->getId(), $kicked_player->getUserId(), $move[Application::DTO_KICKED_FIGURE_INDEX]);
            $kicked_figure->setArea(Application::AREA_HOME);
            $kicked_figure->setPosition($kicked_player->getFirstEmptyHomePosition());
            $kicked_figure->save();
        }

        // Move Figure
        $figure->setArea($move[Application::DTO_TO][Application::DTO_AREA]);
        $figure->setPosition($move[Application::DTO_TO][Application::DTO_POSITION] ?? null);
        $current_player->setFigureByFigureIndex($figure);
        $figure->save();

        // 6. Check Winner
        if ($this->hasPlayerWon($current_player)) {
            $this->setWinner($current_player);
            // Keep rolled dice visible for UI
            $this->state_model->setCurrentDiceRoll(null);
            $this->state_model->save();
            return;
        }

        // 7. Extra roll on six handling
        if ($dice_value === 6) {
            $extra_roll_limit = $this->rule_set_model->getExtraRollOnSixLimit();
            if ($extra_roll_limit !== null) {
                $this->state_model->incrementExtraRollsOnSixUsed();
                $this->state_model->save();

                // check extra roll limit
                if ($this->state_model->getExtraRollsOnSixUsed() >= $extra_roll_limit) {
                    $this->endTurn();
                    return;
                }
            }

            // Extra roll on six granted
            $this->state_model->setCurrentDiceRoll(null);
            $this->state_model->save();
            return;
        }

        // 8. Common turn
        $this->endTurn();
    }

    /**
     * Game Engine - Roll dice and prepare move
     */
    public function rollDice(String $user_id): int {
        $current_player = $this->getCurrentPlayer();

        // Ensure that it is the player's turn
        if ($current_player === null) {
            throw new LogicException('No players turn');
        }
        if ($current_player->getUserId() !== $user_id) {
            throw new LogicException('Not your turn');
        }

        // Check that it hasn't been thrown yet.
        if ($this->state_model->getCurrentDiceRoll() !== null) {
            throw new LogicException('Dice already rolled');
        }

        // Generate dice value
        $dice_value = $this->rollDiceValue();

        // store dice value in DB
        $this->state_model->setCurrentDiceRoll($dice_value);
        $this->state_model->save();

        // Return dice value for UI
        return $dice_value;
    }

    // Game Engine - get a random dice roll
    protected function rollDiceValue(): int {
        return random_int(1, 6);
    }

    /**
     * Game Engine - Calculate possible moves for players
     */
    public function getAvailableMoves(string $user_id, int $dice_value): array {
        $player = $this->getPlayerById($user_id);
        //$moves = [];
        $move = MoveDTO::create();

        foreach ($player->getAllFigures() as $figure) {
            if (!$this->canMoveFigure($player, $figure, $dice_value)) {
                continue;
            }

            $area = $figure->getArea();
            $move = [
                Application::DTO_GAME_TURN => $this->state_model->getCurrentTurnCounter(), 
                Application::DTO_FIGURE_INDEX => $figure->getFigureIndex(),
                Application::DTO_FROM => [
                    Application::DTO_AREA => $area,
                    Application::DTO_POSITION => $figure->getPosition(),
                ],
                Application::DTO_TO => null,
                Application::DTO_ABSOLUTE_TARGET => null,
                Application::DTO_IS_KICK => false,
                Application::DTO_KICKED_PLAYER_ID => null,
                Application::DTO_KICKED_FIGURE_INDEX => null,
                Application::DTO_IS_GOAL_ENTRY => false,
                Application::DTO_IS_LAP_OVERFLOW => false,
            ];

            // HOME -> FIELD
            if ($area === Application::AREA_HOME) {
                $absolute_position = $player->getStartOffset();
                $move[Application::DTO_TO] = [
                    Application::DTO_AREA => Application::AREA_FIELD,
                    Application::DTO_POSITION => 0,
                ];
                $move[Application::DTO_ABSOLUTE_TARGET] = $absolute_position;

                $enemy = $this->getEnemyFiguresOnAbsolutePosition($player, $absolute_position);
                if ($enemy !== null) {
                    $move[Application::DTO_IS_KICK] = true;
                    $move[Application::DTO_KICKED_PLAYER_ID] = $enemy['player']->getUserId();
                    $move[Application::DTO_KICKED_PLAYER_INDEX] = $enemy['player']->getPlayerIndex(); 
                    $move[Application::DTO_KICKED_FIGURE_INDEX] = $enemy['figure']->getFigureIndex();
                }

                $moves[] = $move;
                continue;
            }

            /**
             * FIELD
             */
            if ($area === Application::AREA_FIELD) {
                $relative_position = $figure->getPosition();
                $new_relative_position = $relative_position + $dice_value;
                $can_figure_on_start_field_be_moved = $this->canFigureOnStartFieldBeMoved($player, $dice_value);
                $has_figures_in_home = $this->hasFiguresInHome($player);

                // Check if 6 is rolled, no figure is blocking the start field, at least one figure is at home and this figures must be moves out
                if ($dice_value === 6 && $has_figures_in_home && $this->rule_set_model->getForceLeavingHomeOnSix() && $can_figure_on_start_field_be_moved && $relative_position !== 0) {
                    continue;
                }

                // (Check if 6 is rolled, )player has figure on start field, still figures in home zone and rule start field must be cleared is active
                if ($has_figures_in_home && $this->hasFigureOnStartField($player) && $this->rule_set_model->getStartFieldMustBeCleared() && $can_figure_on_start_field_be_moved && $relative_position !== 0) {
                    continue;
                }

                // Entry to goal area?
                if ($new_relative_position >= self::FIELD_LENGTH) {
                    $steps_into_goal = $new_relative_position - self::FIELD_LENGTH;

                    // Overflow?
                    if ($steps_into_goal >= self::GOAL_LENGTH) {
                        $wrapped_relative_position = $new_relative_position % self::FIELD_LENGTH;
                        $absolute_position = ($player->getStartOffset() + $wrapped_relative_position) % self::FIELD_LENGTH;

                        echo "neue Position: " . $absolute_position;

                        $move[Application::DTO_TO] = [
                            Application::DTO_AREA => Application::AREA_FIELD,
                            Application::DTO_POSITION => $wrapped_relative_position,
                        ];
                        $move[Application::DTO_ABSOLUTE_TARGET] = $absolute_position;
                        $move[Application::DTO_IS_LAP_OVERFLOW] = true;

                        $enemy = $this->getEnemyFiguresOnAbsolutePosition($player, $absolute_position);

                        if ($enemy !== null) {
                            $move[Application::DTO_IS_KICK] = true;
                            $move[Application::DTO_KICKED_PLAYER_ID] = $enemy['player']->getUserId();
                            $move[Application::DTO_KICKED_PLAYER_INDEX] = $enemy['player']->getPlayerIndex(); 
                            $move[Application::DTO_KICKED_FIGURE_INDEX] = $enemy['figure']->getId();
                        }

                        $moves[] = $move;
                        continue;
                    }

                    // Common goal entry
                    $move[Application::DTO_TO] = [
                        Application::DTO_AREA => Application::AREA_GOAL, 
                        Application::DTO_POSITION => $steps_into_goal, 
                    ];

                    $move[Application::DTO_IS_GOAL_ENTRY] = true; 

                    $moves[] = $move;
                    continue;
                }

                // Common field movement
                $absolute_position = ($player->getStartOffset() + $new_relative_position) % self::FIELD_LENGTH;

                $move[Application::DTO_TO] = [
                    Application::DTO_AREA => Application::AREA_FIELD, 
                    Application::DTO_POSITION => $new_relative_position, 
                ];

                $move[Application::DTO_ABSOLUTE_TARGET] = $absolute_position;

                $enemy = $this->getEnemyFiguresOnAbsolutePosition($player, $absolute_position);

                if ($enemy !== null) {
                    $move[Application::DTO_IS_KICK] = true;
                    $move[Application::DTO_KICKED_PLAYER_ID] = $enemy['player']->getUserId();
                    $move[Application::DTO_KICKED_PLAYER_INDEX] = $enemy['player']->getPlayerIndex(); 
                    $move[Application::DTO_KICKED_FIGURE_INDEX] = $enemy['figure']->getFigureIndex();
                }

                $moves[] = $move;
                continue;
            }

            /**
             * GOAL
             */
            if ($area === Application::AREA_GOAL) {
                $new_goal_position = $figure->getPosition() + $dice_value;

                $move[Application::DTO_TO] = [
                    Application::DTO_AREA => Application::AREA_GOAL, 
                    Application::DTO_POSITION => $new_goal_position, 
                ];

                $moves[] = $move;
                continue;
            }
        }

        // No move possible -> Offer pass-Move 
        if (empty($moves)) {
            $moves[] = [
                Application::DTO_FIGURE_INDEX => null,
                Application::DTO_FROM => null,
                Application::DTO_TO => null,
                Application::DTO_IS_PASS => true,
            ];
        }

        return $moves;
    }

    // Game Engine - Check if figure can move
    // This is the MAIN METHOD for GAME ENGINE LOGIC
    private function canMoveFigure(GameStatePlayerModel $player, GameStateFigureModel $figure, int $dice_value): bool {
        $area = $figure->getArea();

        /**
         * 1. Step - Figure is in goal area already
         */
        if ($area === Application::AREA_GOAL) {
            $current_goal_position = $figure->getPosition();
            $new_goal_position = $current_goal_position + $dice_value;

            // too far for finish in goal area
            if ($new_goal_position >= self::GOAL_LENGTH) {
                return false;
            }

            // check strict goal order
            if ($this->isGoalPositionBlockedByStrictOrder($player, $figure, $new_goal_position)) {
                return false;
            }

            // check if position in goal area is already occupied 
            if ($this->isGoalPositionOccupied($player, $new_goal_position)) {
                return false;
            }
            return true;
        }

        /**
         * 2. Step - Figure is still in home area
         */

        if ($area === Application::AREA_HOME) {
            // Figure can only leave home area with a six
            if ($dice_value !== 6) {
                return false;
            }

            $start_offset = $player->getStartOffset();

            // Is own figure blocked?
            if (!$this->rule_set_model->getAllowStackOwnFigures()) {
                if ($this->isOwnFigureOnAbsolutePosition($player, $start_offset)) {
                    return false;
                }
            }

            // Perhaps the start field of the player must be cleared
            if ($this->rule_set_model->getStartFieldMustBeCleared()) {
                if ($this->isStartFieldBlockedByPlayer($player)) {
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

                // $dice_roll is too high - overflow
                if ($steps_into_goal >= self::GOAL_LENGTH) {
                    // complete another lap optional?
                    if (!$this->rule_set_model->getForceExtraLapOnOverflow()) {
                        return false;
                    }

                    // calculate new field / board position
                    $wrapped_relative_position = $new_relative_position % self::FIELD_LENGTH;
                    $absolute_target = ($player->getStartOffset() + $wrapped_relative_position) %self::FIELD_LENGTH;

                    // check stack
                    if (!$this->rule_set_model->getAllowStackOwnFigures()) {
                        if ($this->isOwnFigureOnAbsolutePosition($player, $absolute_target)) {
                            return false;
                        }
                    }

                    // no other figure is allowed to move
                    return true;
                }

                // strict goal order active?
                if ($this->isGoalPositionBlockedByStrictOrder($player, $figure, $steps_into_goal)) {
                    return false;
                }

                // check if position in goal area is already occupied 
                if ($this->isGoalPositionOccupied($player, $steps_into_goal)) {
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

        return false;
    }

    /**
     * Game Engine - End turn
     */
    private function endTurn(): void {
        $this->state_model->setCurrentDiceRoll(null);
        $this->state_model->resetLeaveHomeAttemptsUsed();
        $this->state_model->resetExtraRollsOnSixUsed();

        // Set next player
        $new_player_index = ($this->state_model->getCurrentPlayerIndex() + 1) % $this->getPlayerCount();
        $this->state_model->setCurrentPlayerIndex($new_player_index);
        $this->state_model->incrementCurrentTurnCounter();
        $this->state_model->save();
    }

    /**
     * Game Engine - Pass turn
     * Called when the player cannot make a move or deliberately passes
     */
    public function passTurn(string $user_id): void {
        // Check if it's users turn
        $this->assertPlayerTurn($user_id);

        $current_player = $this->getCurrentPlayer();

        $dice_value = $this->state_model->getCurrentDiceRoll();

        // If there is no dice value, you cannot pass
        if ($dice_value === null) {
            throw new LogicException('Cannot pass without rolling the dice');
        }

        // Check if the player can move any figures at all
        $available_moves = $this->getAvailableMoves($user_id, $dice_value);

        if (!empty($available_moves)) {
            // Moves available → Passing not allowed unless explicitly desired
            // We only allow passing here if the player cannot move a character
            $can_move = false;
            foreach ($available_moves as $move) {
                if (empty($move[Application::DTO_IS_PASS])) {
                    $can_move = true;
                    break;
                }
            }

            if ($can_move) {
                throw new LogicException('Cannot pass when moves are available');
            }
        }

        // Reset dice value
        $this->state_model->setCurrentDiceRoll(null);
        $this->state_model->save();

        // Check extra roll on six: If there are still throws left, the player stays on their turn
        if ($dice_value === 6) {
            $extra_roll_limit = $this->rule_set_model->getExtraRollOnSixLimit();
            if ($extra_roll_limit !== null) {
                $this->state_model->incrementExtraRollsOnSixUsed();
                if ($this->state_model->getExtraRollsOnSixUsed() < $extra_roll_limit) {
                    return; // The player may roll the dice again
                }
            }
        }

        // Check leave home attempts: If figure is in the house and there are still attempts left
        if ($this->hasFiguresInHome($current_player)) {
            $variant = $this->rule_set_model->getLeaveHomeAttemptVariant();
            $max_attempts = $this->rule_set_model->getLeaveHomeAttemptsMax() - 1;

            if ($variant !== null && $this->state_model->getLeaveHomeAttemptsUsed() < $max_attempts) {
                $this->state_model->incrementLeaveHomeAttemptsUsed();
                $this->state_model->save();
                return; // The player may roll the dice again
            }
        }

        // End Turn
        $this->endTurn();
    }

    // Game Engine - Validate move turn with current game urn  counter
    private function validateTurnCounter(int $move_turn_counter): void {
        if ($move_turn_counter !== $this->state_model->getCurrentTurnCounter()) {
            throw new LogicException('Stale move');
        }
    }

    // Game Engine - Set next player in line as active current player
    private function nextPlayer(): void {
        $next_player_index = ($this->state_model->getCurrentPlayerIndex() + 1) % $this->getPlayerCount();
        $this->state_model->setCurrentPlayerIndex($next_player_index);
        $this->state_model->setCurrentDiceRoll(null);
    }

    // Game Engine - Check if it's current players turn
    private function assertPlayerTurn(String $user_id): void {
        if ($this->getCurrentPlayer()->getUserId() !== $user_id) {
            throw new LogicException('Not your turn');
        }
    }

    // Game Engine - get absolute position on the field of given figure
    public function getAbsoluteFieldPosition(GameStatePlayerModel $player, GameStateFigureModel $figure): int {
        if ($figure->getArea() !== Application::AREA_FIELD) {
            return $figure->getPosition();
            throw new LogicException('Figure is not on field');
        }

        $start_offset = $player->getStartOffset(); // 0, 10, 20, 30
        $relative_position = $figure->getPosition();

        $absolute_position = ($start_offset + $relative_position) % self::FIELD_LENGTH;

        return $absolute_position;
    }

    // Game Engine - Can figure on start field be moved
    // This method returns true if at least one figure on the start field can be moved or no figure is on the start field
    private function canFigureOnStartFieldBeMoved(GameStatePlayerModel $player, int $dice_value): bool {
        $figures = $player->getAllFigures();
        $on_start_field = [false, false, false, false]; 
        $figures_on_start_field = [];

        // Check if any and which figure is on start field
        for ($i = 0; $i < count($figures); $i++) {
            // Only figures on field has to be checked
            if ($figures[$i]->getArea() !== Application::AREA_FIELD) {
                continue;
            }

            // If figure is on start field keep that in mind
            if ($figures[$i]->getPosition() === 0) {
                $on_start_field[$i] = true;
                $figures_on_start_field[] = $i;
            }
        }

        if (count($figures_on_start_field) > 0) {
            for ($j = 0; $j < count($figures); $j++) {
                // Don't check the new position of figure that starts on start field
                if ($figures_on_start_field[0] === $j) {
                    continue;
                }

                if ($dice_value === $figures[$j]->getPosition()) {
                    return false;
                }
            }
        }

        return true;
    }

    // Game Engine - Check if start field is blocked
    private function isStartFieldBlockedByPlayer(GameStatePlayerModel $player): bool {
        $start_offset = $player->getStartOffset();
        foreach ($player->getAllFigures() as $figure) {
            if ($figure->getArea() !== Application::AREA_FIELD) {
                continue;
            }

            $absolute_position = $this->getAbsoluteFieldPosition($player, $figure);

            if ($absolute_position === $start_offset) {
                return true;
            }
        }

        return false;
    }

    // Game Engine - Check if start field is blocked
    private function isStartFieldBlocked(GameStatePlayerModel $player): bool {
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

    // Game Engine - Get the absolute position of enemy figure
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
    private function isGoalPositionBlockedByStrictOrder(GameStatePlayerModel $player, GameStateFigureModel $current_figure, int $target_goal_position): bool {
        if (!$this->rule_set_model->getStrictGoalOrder()) {
            return false;
        }

        foreach ($player->getAllFigures() as $figure) {
            if ($current_figure === $figure) {
                continue;
            }

            if ($figure->getArea() !== Application::AREA_GOAL) {
                continue;
            }

            if ($figure->getPosition() < $target_goal_position) {
                return true;
            }
        }

        return false;
    }

    private function isGoalPositionOccupied(GameStatePlayerModel $player, int $target_goal_position): bool {
        foreach ($player->getAllFigures() as $figure) {
            if ($figure->getArea() === Application::AREA_GOAL && $figure->getPosition() === $target_goal_position) {
                return true;
            }
        }
        return false;
    }

    // Game Engine - Check if player has other movable figures
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

    // Game Engine - Check if player has won the game
    private function hasPlayerWon(GameStatePlayerModel $player): bool {
        $slots = array_fill(0, self::GOAL_LENGTH, false); 
        foreach ($player->getAllFigures() as $figure) {
            $position = $figure->getPosition();
            if ($figure->getArea() !== Application::AREA_GOAL) {
                return false;
                continue;
            }

            if ($position < 0 || $position >= self::GOAL_LENGTH) {
                return false;
                continue;
            }

            if ($slots[$position]) {
                return false;
                continue;
            }
            $slots[$position] = true;
        }
        return !in_array(false, $slots, true);
    }

    // Game Engine - Set winner of the game
    private function setWinner(GameStatePlayerModel $player): void {
        $this->state_model->setWinnerUserId($player->getUserId());
        $this->state_model->save();
        $this->status = Application::STATUS_FINISHED;
        $this->save();
    }

    /**
     * Database
     */

    // Get all games
    public static function getAllGames(): array {
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
                    g.%s, 
                    COUNT(p.%s) AS player_count, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
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
                GROUP BY g.%s
                ORDER BY g.%s DESC",
                
                Application::ID,
                Application::NAME,
                Application::CREATED_BY_USER_ID,
                Application::USERNAME,
                Application::STATUS, 
                Application::IS_PRIVATE, 
                Application::IS_LOCKED, 
                Application::IS_TEST_GAME, 
                Application::CREATED_AT,
                Application::UPDATED_AT, 
                Application::USER_ID,
                Application::ALLOW_BOTS, 
                Application::ALL_FIGURES_START_AT_HOME, 
                Application::LEAVE_HOME_ATTEMPT, 
                Application::LEAVE_HOME_ATTEMPTS_MAX, 
                Application::EXTRA_ROLL_ON_SIX_LIMIT, 
                Application::FORCE_LEAVING_HOME_ON_SIX, 
                Application::FORCE_CAPTURE_ENEMY_FIGURES, 
                Application::FORCE_EXTRA_LAP_ON_OVERFLOW, 
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
                    g.%s, 
                    COUNT(p.%s) AS player_count, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
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
                Application::IS_TEST_GAME, 
                Application::CREATED_AT,
                Application::UPDATED_AT, 
                Application::USER_ID,
                Application::ALLOW_BOTS, 
                Application::ALL_FIGURES_START_AT_HOME, 
                Application::LEAVE_HOME_ATTEMPT, 
                Application::LEAVE_HOME_ATTEMPTS_MAX, 
                Application::EXTRA_ROLL_ON_SIX_LIMIT, 
                Application::FORCE_LEAVING_HOME_ON_SIX, 
                Application::FORCE_CAPTURE_ENEMY_FIGURES, 
                Application::FORCE_EXTRA_LAP_ON_OVERFLOW, 
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

    // Database - Get all games created by the given user
    public static function getAllGamesCreatedByUser(string $user_id): array {
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
                    g.%s, 
                    COUNT(p.%s) AS player_count, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
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
                WHERE g.%s = :created_by_user_id
                GROUP BY g.%s
                ORDER BY g.%s DESC",
                
                Application::ID,
                Application::NAME,
                Application::CREATED_BY_USER_ID,
                Application::USERNAME,
                Application::STATUS, 
                Application::IS_PRIVATE, 
                Application::IS_LOCKED, 
                Application::IS_TEST_GAME, 
                Application::CREATED_AT,
                Application::UPDATED_AT, 
                Application::USER_ID,
                Application::ALLOW_BOTS, 
                Application::ALL_FIGURES_START_AT_HOME, 
                Application::LEAVE_HOME_ATTEMPT, 
                Application::LEAVE_HOME_ATTEMPTS_MAX, 
                Application::EXTRA_ROLL_ON_SIX_LIMIT, 
                Application::FORCE_LEAVING_HOME_ON_SIX, 
                Application::FORCE_CAPTURE_ENEMY_FIGURES, 
                Application::FORCE_EXTRA_LAP_ON_OVERFLOW, 
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

                Application::CREATED_BY_USER_ID,
                Application::ID,
                Application::CREATED_AT
            ),
            ['created_by_user_id' => $user_id]
        );
        return array_map(fn($row) => self::fromArray($row), $rows);
    }

    // Database - Get all games with the given user as an participant
    public static function getAllGamesWithUserParticipating(string $user_id): array {
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
                    g.%s, 
                    COUNT(p.%s) AS player_count, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
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
                WHERE p.%s = :user_id
                GROUP BY g.%s
                ORDER BY g.%s DESC",
                
                Application::ID,
                Application::NAME,
                Application::CREATED_BY_USER_ID,
                Application::USERNAME,
                Application::STATUS, 
                Application::IS_PRIVATE, 
                Application::IS_LOCKED, 
                Application::IS_TEST_GAME, 
                Application::CREATED_AT,
                Application::UPDATED_AT, 
                Application::USER_ID,
                Application::ALLOW_BOTS, 
                Application::ALL_FIGURES_START_AT_HOME, 
                Application::LEAVE_HOME_ATTEMPT, 
                Application::LEAVE_HOME_ATTEMPTS_MAX, 
                Application::EXTRA_ROLL_ON_SIX_LIMIT, 
                Application::FORCE_LEAVING_HOME_ON_SIX, 
                Application::FORCE_CAPTURE_ENEMY_FIGURES, 
                Application::FORCE_EXTRA_LAP_ON_OVERFLOW, 
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

                Application::USER_ID,
                Application::ID,
                Application::CREATED_AT
            ),
            ['user_id' => $user_id]
        );
        return array_map(fn($row) => self::fromArray($row), $rows);
    }

    // Database - Get all games with given user involved
    public static function getAllGamesWithUserInvolved(string $user_id): array{
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
                    g.%s, 
                    COUNT(p.%s) AS player_count, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
                    r.%s, 
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
                WHERE p.%s = :user_id OR g.%s = :user_id
                GROUP BY g.%s
                ORDER BY g.%s DESC",
                
                Application::ID,
                Application::NAME,
                Application::CREATED_BY_USER_ID,
                Application::USERNAME,
                Application::STATUS, 
                Application::IS_PRIVATE, 
                Application::IS_LOCKED, 
                Application::IS_TEST_GAME, 
                Application::CREATED_AT,
                Application::UPDATED_AT, 
                Application::USER_ID,
                Application::ALLOW_BOTS, 
                Application::ALL_FIGURES_START_AT_HOME, 
                Application::LEAVE_HOME_ATTEMPT, 
                Application::LEAVE_HOME_ATTEMPTS_MAX, 
                Application::EXTRA_ROLL_ON_SIX_LIMIT, 
                Application::FORCE_LEAVING_HOME_ON_SIX, 
                Application::FORCE_CAPTURE_ENEMY_FIGURES, 
                Application::FORCE_EXTRA_LAP_ON_OVERFLOW, 
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

                Application::USER_ID,
                Application::CREATED_BY_USER_ID, 
                Application::ID,
                Application::CREATED_AT
            ),
            ['user_id' => $user_id]
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
                    r.%s, 
                    r.%s, 
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
                Application::ALL_FIGURES_START_AT_HOME, 
                Application::LEAVE_HOME_ATTEMPT, 
                Application::LEAVE_HOME_ATTEMPTS_MAX, 
                Application::EXTRA_ROLL_ON_SIX_LIMIT, 
                Application::FORCE_LEAVING_HOME_ON_SIX, 
                Application::FORCE_CAPTURE_ENEMY_FIGURES, 
                Application::FORCE_EXTRA_LAP_ON_OVERFLOW, 
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

        // Load complete State of the Game
        $state = static::fetchOne(
            sprintf(
                "SELECT 
                    s.%s, 
                    s.%s, 
                    s.%s, 
                    s.%s, 
                    s.%s, 
                    s.%s, 
                    s.%s, 
                    s.%s, 
                    s.%s
                FROM %s s
                WHERE s.%s = :game_id
                LIMIT 1", 

                Application::GAME_ID, 
                Application::CURRENT_PLAYER_INDEX, 
                Application::CURRENT_DICE_ROLL, 
                Application::CURRENT_TURN_COUNTER, 
                Application::LEAVE_HOME_ATTEMPTS_USED, 
                Application::EXTRA_ROLLS_ON_SIX_USED, 
                Application::WINNER_USER_ID, 
                Application::CREATED_AT, 
                Application::UPDATED_AT, 

                Application::TABLE_STATE, 

                Application::GAME_ID
            ), 
            ['game_id' => $game_id]
        );

        $game->state_model = GameStateModel::fromArray($state);

        // Load all players of the game
        $players = static::fetchAll(
            sprintf(
                "SELECT
                    p.%s, 
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
                Application::PLAYER_INDEX, 
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

        // Standard games are no test games
        if (!key_exists(Application::IS_TEST_GAME, $game_options)) $game_options[Application::IS_TEST_GAME] = 0;

        try {
            $this->db->beginTransaction();

            // Insert game
            $this->execute(
                sprintf(
                    "INSERT INTO %s (%s, %s, %s, %s, %s, %s, %s, created_at, updated_at)
                    VALUES (:id, :name, :created_by_user_id, :status, :is_private, :is_locked, :is_test_game, NOW(), NOW())",
                    Application::TABLE_GAMES,
                    Application::ID,
                    Application::NAME, 
                    Application::CREATED_BY_USER_ID,
                    Application::STATUS, 
                    Application::IS_PRIVATE, 
                    Application::IS_LOCKED, 
                    Application::IS_TEST_GAME
                ),
                [
                    'id' => $game_id,
                    'name' => $game_name, 
                    'created_by_user_id' => $user_id,
                    'status' => Application::STATUS_WAITING, 
                    'is_private' => $game_options[Application::IS_PRIVATE], 
                    'is_locked' => $game_options[Application::IS_LOCKED], 
                    'is_test_game' => $game_options[Application::IS_TEST_GAME]
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

    // Save current game state - only the GameModel
    public function save(): bool {
        return $this->updateGame($this->toArray());
    }

    // Database - Update current state of the game
    private function updateGame(array $game_array) {
        return static::execute(
            sprintf(
                "UPDATE 
                    games
                SET
                    name = :name, 
                    created_by_user_id = :created_by_user_id, 
                    status = :status, 
                    is_private = :is_private, 
                    is_locked = :is_locked 
                WHERE 
                    id = :id", 
                
                Application::TABLE_GAMES, 

                Application::NAME, 
                Application::CREATED_BY_USER_ID, 
                Application::STATUS, 
                Application::IS_PRIVATE, 
                Application::IS_LOCKED, 

                Application::ID
            ), [
                'name' => $game_array[Application::NAME], 
                'created_by_user_id' => $game_array[Application::CREATED_BY_USER_ID], 
                'status' => $game_array[Application::STATUS], 
                'is_private' => (int)$game_array[Application::IS_PRIVATE], 
                'is_locked' => (int)$game_array[Application::IS_LOCKED], 
                'id' => $game_array[Application::ID]
            ]
        );
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
                    %s 
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
    // Game - Update game status helper - Start game
    public function startGame(): void {
        $this->updateStatus(Application::STATUS_RUNNING);
        $this->setStatus(Application::STATUS_RUNNING);
    }

    // Game - Update game status helper - finish game
    public function finishGame(): void {
        $this->updateStatus(Application::STATUS_FINISHED);
        $this->setStatus(Application::STATUS_FINISHED);
    }

    // Game - Update game status helper - Set game to waiting
    public function pauseGame(): void {
        $this->updateStatus(Application::STATUS_WAITING);
        $this->setStatus(Application::STATUS_WAITING);
    }

    // Game - Update game status helper - Cancel game
    public function cancelGame(): void {
        $this->updateStatus(Application::STATUS_CANCELLED);
        $this->setStatus(Application::STATUS_CANCELLED);
    }

    // Game - Join game - player 
    public function join(string $user_id): bool {
        if ($this->status !== Application::STATUS_WAITING) {
            throw new DomainException('Game cannot be joined');
        }

        if ($this->hasPlayer($user_id)) {
            throw new DomainException('User already joined');
        }

        if ($this->getPlayerCount() >= $this->getPlayerMax()) {
            throw new DomainException('Game is full');
        }

        // ToDo: add Player
        try {
            $this->db->beginTransaction();

            GameStatePlayerModel::addPlayer($this->getId(), $user_id, $this->getPlayerCount(), $this->rule_set_model->getAllFiguresStartAtHome());

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
    // Helper - Get Game State Snapshot
    public function getGameStateSnapshot(): array {
        $players = [];

        foreach($this->getAllPlayers() as $player) {
            $figures = [];

            foreach ($player->getAllFigures() as $figure) {
                $figures[] = [
                    Application::DTO_FIGURE_INDEX => $figure->getFigureIndex(), 
                    Application::DTO_AREA => $figure->getArea(), 
                    //Application::DTO_POSITION => $figure->getPosition()
                    Application::DTO_POSITION => ($this->getAbsoluteFieldPosition($player, $figure)) ?? $figure->getPosition()
                ];
            }

            $players[] = [
                Application::DTO_USER_ID => $player->getUserId(), 
                Application::DTO_PLAYER_INDEX => $player->getPlayerIndex(), 
                Application::DTO_USERNAME => $player->getUsername(), 
                Application::DTO_FIGURES => $figures
            ];
        }

        return [
            Application::DTO_GAME_ID => $this->getId(), 
            Application::DTO_GAME_NAME => $this->getName(), 
            Application::DTO_GAME_STATUS => $this->getStatus(), 
            Application::DTO_CURRENT_PLAYER_ID => $this->getCurrentPlayer()->getUserId(), 
            Application::DTO_CURRENT_PLAYER_INDEX => $this->getCurrentPlayer()->getPlayerIndex(), 
            Application::DTO_CURRENT_PLAYER_USERNAME => $this->getCurrentPlayer()->getUsername(), 
            Application::DTO_WINNER_USER_ID => $this->state_model->getWinnerUserId(), 
            Application::DTO_WINNER_PLAYER_INDEX => $this->getWinnerPlayerIndex(), 
            Application::DTO_CURRENT_DICE_ROLL => $this->getStateModel()->getCurrentDiceRoll(), 
            Application::DTO_PLAYERS => $players
        ];
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
        $game->is_test_game = $row['is_test_game'] ?? null;

        if (array_key_exists(Application::USERNAME, $row)) $game->created_by_user_name = $row[Application::USERNAME];
        if (array_key_exists(Application::PLAYER_COUNT, $row)) $game->player_count = (int) $row[Application::PLAYER_COUNT];

        $game->rule_set_model = GameRuleSetModel::fromArray($row) ?? null;

        $game->created_at = $row['created_at'];
        $game->updated_at = $row['updated_at'];
        return $game;
    }

    // Helper - Create Array from GameModel
    private function toArray(): array {
        $game_array[Application::ID] = $this->id;
        $game_array[Application::NAME] = $this->name;
        $game_array[Application::CREATED_BY_USER_ID] = $this->created_by_user_id;
        $game_array[Application::STATUS] = $this->status;
        $game_array[Application::IS_PRIVATE] = $this->is_private;
        $game_array[Application::IS_LOCKED] = $this->is_locked;
        $game_array[Application::IS_TEST_GAME] = $this->is_test_game;

        return $game_array;
    }

    // Helper - Get Winner 
    public function getWinner(): ?GameStatePlayerModel {
        if ($this->state_model->getWinnerUserId() === null) {
            return null;
        }

        foreach ($this->player_array as $player) {
            if ($player->getUserId() === $this->state_model->getWinnerUserId()) {
                return $player;
            }
        }
        return null;
    }

    // Helper - Get Winner Player Index
    private function getWinnerPlayerIndex(): ?int {
        if ($this->state_model->getWinnerUserId() === null) {
            return null;
        }

        foreach ($this->player_array as $player) {
            if ($player->getUserId() === $this->state_model->getWinnerUserId()) {
                return $player->getPlayerIndex();
            }
        }
        return null;
    }

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

    // Helper -  Is solo test game
    public function isTestGame(): bool {
        return $this->is_test_game;
    }

    // Helper - Check for available player slots
    public function isFull(): bool {
        return $this->getPlayerCount() >= $this->getPlayerMax();
    }

    // Helper - get player offset
    public function getPlayerStartOffset(int $player_index): int {
        return ( self::FIELD_LENGTH / self::PLAYERS_MAX ) * $player_index;
    }

    // Helper - Check if given user is already a player of the game
    public function isParticipant(UserModel $user): bool {
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

    // Helper - Check if given user is creator of the game
    public function isCreator(UserModel $user): bool {
        if ($this->created_by_user_id === $user->getId()) {
            return true;
        }
        return false;
    }

    // Helper - Check if this game has participants
    public function hasParticipants(): bool {
        if ($this->getPlayerCount() > 0) {
            return true;
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

    // Helper - Get current player
    public function getCurrentPlayer(): GameStatePlayerModel {
        return $this->getPlayerByPlayerIndex($this->getStateModel()->getCurrentPlayerIndex());
    }

    // Helper - Is players turn
    public function isPlayersTurn(UserModel $user): bool{
        if ($this->getCurrentPlayer()->getUserId() === $user->getId()) {
            return true;
        }
        return false;
    }

    // Helper - Clone existing game
    public function cloneGameWithOnePlayer(): ?string {
        if ($this->is_test_game) {
            return null;
        }
        
        $game_user_id = $this->getCreatedByUserId();
        $game_name = 'Solo Test - ' . $this->name;

        $game_options = [
            Application::IS_PRIVATE => (int)$this->IsPrivate(), 
            Application::IS_LOCKED => (int)$this->isLocked(), 
            Application::IS_TEST_GAME => 1,
        ];

        $rule_set = [
            Application::ALLOW_BOTS => $this->rule_set_model->getAllowBots(),
            Application::LEAVE_HOME_ATTEMPT => $this->rule_set_model->getLeaveHomeAttemptVariant(), 
            Application::LEAVE_HOME_ATTEMPTS_MAX => $this->rule_set_model->getLeaveHomeAttemptsMax(), 
            Application::EXTRA_ROLL_ON_SIX_LIMIT => $this->rule_set_model->getExtraRollOnSixLimit(),
            Application::FORCE_EXTRA_LAP_ON_OVERFLOW => $this->rule_set_model->getForceExtraLapOnOverflow(),
            Application::ALLOW_STACK_OWN_FIGURES => $this->rule_set_model->getAllowStackOwnFigures(),
            Application::STRICT_GOAL_ORDER => $this->rule_set_model->getStrictGoalOrder(),
            Application::START_FIELD_MUST_BE_CLEARED => $this->rule_set_model->getStartFieldMustBeCleared(),
        ];

        return $this->create($game_user_id, $game_name, $game_options, $rule_set);
    }

    // Helper - Can user use triple roll
    public function canUseTripleRoll($user): bool {
        return true;
    }

    // Helper - has any available move
    private function hasAvailableMoves(GameStatePlayerModel $player, int $dice_value): bool {
        foreach ($player->getAllFigures() as $figure) {
            if ($this->canMoveFigure($player, $figure, $dice_value)) {
                return true;
            }
        }
        return false;
    }

    // Helper - Check is move is already in list
    private function isMoveInList(array $move, array $available_moves): bool {
        foreach ($available_moves as $available_move) {
            if (
                $move[Application::DTO_FIGURE_INDEX] === $available_move[Application::DTO_FIGURE_INDEX] 
                && $move[Application::DTO_TO][Application::DTO_AREA] === $available_move[Application::DTO_TO][Application::DTO_AREA] 
                && ($move[Application::DTO_TO][Application::DTO_POSITION] ?? null) === ($available_move[Application::DTO_TO][Application::DTO_POSITION] ?? null) 
                && ($move[Application::DTO_IS_KICK] ?? false) === ($available_move[Application::DTO_IS_KICK] ?? false) 
                && ($move[Application::DTO_IS_GOAL_ENTRY] ?? null) === ($available_move[Application::DTO_IS_GOAL_ENTRY] ?? null) 
                && ($move[Application::DTO_IS_LAP_OVERFLOW] ?? null) === ($available_move[Application::DTO_IS_LAP_OVERFLOW] ?? null)
            ) {
                return true;
            }
        }
        return false;
    }

    // Helper - Check if all figures are in home area
    private function allFiguresInHome(GameStatePlayerModel $player): bool {
        foreach ($player->getAllFigures() as $figure) {
            if ($figure->getArea() !== Application::AREA_HOME) {
                return false;
            }
        }
        return true;
    }
    // Helper - Checks if the player still has pieces in their home base.
    private function hasFiguresInHome(GameStatePlayerModel $player): bool {
        foreach ($player->getAllFigures() as $figure) {
            if ($figure->getArea() === Application::AREA_HOME) {
                return true;
            }
        }
        return false;
    }

    // Helper - Has no Figure on Board
    private function hasNoFigureOnBoard(GameStatePlayerModel $player): bool {
        foreach ($player->getAllFigures() as $figure) {
            if ($figure->getArea() === Application::AREA_FIELD) {
                return false;
            }
        }
        return true;
    }

    // Helper - Has Figure on start field
    private function hasFigureOnStartField(GameStatePlayerModel $player): bool {
        foreach ($player->getAllFigures() as $figure) {
            if ($figure->getArea() === Application::AREA_FIELD && $figure->getPosition() === 0) {
                return true;
            }
        }
        return false;
    }

    // Helper - Allowed to edit Rule all_figures_start_at_home
    public function editAllowedRuleAllFiguresStartAtHome(): bool {
        if ($this->hasParticipants()) {
            return false;
        }
        return true;
    }

    // Helper - Debug State 
    // ToDo: check if this is ever used and useful!
    public function getDebugState(string $user_id) {
        if ($this->state_model->getCurrentDiceRoll() === null) {
            return [
                Application::CURRENT_PLAYER_INDEX => $this->state_model->getCurrentPlayerIndex(), 
                Application::CURRENT_DICE_ROLL => 'NULL', 
                Application::AVAILABLE_MOVES => [], 
                Application::EXTRA_ROLLS_ON_SIX_USED => $this->state_model->getExtraRollsOnSixUsed()
            ];
        }
        return [
            Application::CURRENT_PLAYER_INDEX => $this->state_model->getCurrentPlayerIndex(), 
            Application::CURRENT_DICE_ROLL => $this->state_model->getCurrentDiceRoll(), 
            Application::AVAILABLE_MOVES => $this->getAvailableMoves($user_id, $this->state_model->getCurrentDiceRoll()), 
            Application::EXTRA_ROLLS_ON_SIX_USED => $this->state_model->getExtraRollsOnSixUsed()
        ];
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
    public function getAllPlayers(): array {
        return $this->player_array;
    }
    
    // Get player given by player id
    public function getPlayerById(string $player_id): GameStatePlayerModel {
        return GameStatePlayerModel::getPlayerById($this->id, $player_id);
    }

    public function getPlayerIndexByPlayerId(string $player_id) {
        return $this->getPlayerById($player_id)->getPlayerIndex();
    }
    
    // Get player given by player id
    public function getPlayerByPlayerIndex(int $player_index): GameStatePlayerModel {
        return GameStatePlayerModel::getPlayerByPlayerIndex($this->id, $player_index);
    }

    /**
     * Setter
     */
    // Setter - Set status
    public function setStatus(string $status): bool {
        if (
            $status === Application::STATUS_CANCELLED 
            || $status === Application::STATUS_FINISHED 
            || $status === Application::STATUS_FINISHED 
            || $status === Application::STATUS_RUNNING 
            || $status === Application::STATUS_WAITING 
        ) {
            $this->status = $status;
            return true;
        }
        return false;
    }
}