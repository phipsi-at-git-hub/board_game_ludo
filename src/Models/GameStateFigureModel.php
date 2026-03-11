<?php 
// src/Models/GameStateFigureModel.php
namespace App\Models;

use App\Constants\Application;

final class GameStateFigureModel extends BaseModel {
    // ToDo: Use constant from application.php 
    private string $game_id;
    private string $user_id;
    private string $user_name;
    private int $figure_index;
    private int $position;
    private string $area;
    private string $created_at;
    private string $updated_at;

    // Find game state figures by game id
    public static function findByGameId(string $game_id): array {
        return static::fetchAll(
            "SELECT * 
            FROM game_state_figures 
            WHERE game_id = :game_id",
            ['game_id' => $game_id]
        );
    }

    // Find game state players for given game
    public static function findByFigureIndex(string $game_id, string $user_id, int $figure_index): self {
        $row = static::fetchOne(
            sprintf(
                "SELECT * 
                FROM game_state_figures 
                WHERE 
                    game_id = :game_id 
                    AND user_id = :user_id 
                    AND figure_index = :figure_index"
            ),
            [
                'game_id' => $game_id,
                'user_id' => $user_id, 
                'figure_index' => $figure_index 
            ]
        );

        return self::fromArray($row);
    }

    // Find game state figures by game id and player id
    public static function findByGameIdAndPlayerId(string $game_id, string $player_id): array {
        $rows = static::fetchAll(
            sprintf(
                "SELECT *
                FROM game_state_figures
                WHERE
                    game_id = :game_id AND 
                    user_id = :user_id"
            ), [
                'game_id' => $game_id, 
                'user_id' => $player_id
            ]
        );
        return array_map(fn($row) => self::fromArray($row), $rows);
    }

    // Create initial figures for given game
    public static function createInitialFigureSet(string $game_id, string $user_id): void {
        $figures = [];
        for ($i = 0; $i < 4; $i++) {
            static::execute(
                "INSERT INTO game_state_figures
                 (game_id, user_id, figure_index, position, area, created_at, updated_at)
                 VALUES
                 (:game_id, :user_id, :figure_index, :position, 'home', NOW(), NOW())",
                [
                    'game_id' => $game_id,
                    'user_id' => $user_id,
                    'figure_index' => $i, 
                    'position' => $i
                ]
            );
        }
    }

    // Store complete GameStateFigureModel
    public function save(): bool {
        return $this->updateFigure($this->game_id, $this->user_id, $this->figure_index, $this->toArray());
    }

    // Store position
    public function savePosition(string $game_id, string $user_id, int $figure_index, int $figure_position): bool {
        $game_state_figure_array = $this->toArray();
        $game_state_figure_array[Application::POSITION] = $figure_position;

        return $this->updateFigure($game_id, $user_id, $figure_index, $game_state_figure_array);
    }

    // Store position
    public function saveArea(string $game_id, string $user_id, int $figure_index, string $figure_area): bool {
        $game_state_figure_array = $this->toArray();
        $game_state_figure_array[Application::AREA] = $figure_area; 
        var_dump($game_state_figure_array);

        return $this->updateFigure($game_id, $user_id, $figure_index, $game_state_figure_array);
    }

    // Update given figures for given game
    public static function updateFigure(string $game_id, string $user_id, int $figure_index, array $figure_array): bool {
        return static::execute(
            sprintf(
                "UPDATE game_state_figures SET area = :area, position = :position WHERE game_id = :game_id AND user_id = :user_id AND figure_index = :figure_index"
            ), [
                'position' => $figure_array[Application::POSITION], 
                'area' => $figure_array[Application::AREA], 
                'game_id' => $game_id, 
                'user_id' => $user_id, 
                'figure_index' => $figure_index
            ]
        );
    }

    // Reset given figure for given game
    public static function resetFigure(): void {}

    // Delete all figures of given game
    public static function removeAllFigures($game_id): bool {
        return static::execute(
            "DELETE FROM game_state_figure WHERE game_id = :game_id",
            ['game_id' => $game_id]
        );
    }

    // Delete all figures of given user
    public static function removeAllUserFigures(string $game_id, string $user_id): bool {
        return static::execute(
            "DELETE FROM game_state_figures WHERE game_id = :game_id AND user_id = :user_id",
            ['game_id' => $game_id, 'user_id' => $user_id]
        );
    }

    // Getter
    // Getter - Get game id
    public function getGameId(): string {
        return $this->game_id;
    }

    // Getter - Get user id
    public function getUserId(): string {
        return $this->user_id;
    }

    // Getter - Get username
    public function getUsername(): string {
        return $this->user_name;
    }

    // Getter - Get figure index
    public function getFigureIndex(): string {
        return $this->figure_index;
    }

    // Getter - Get position
    public function getPosition(): string {
        return $this->position;
    }

    // Getter - Get area
    public function getArea(): string {
        return $this->area;
    }

    // Getter - Get created at
    public function getCreatedAt(): string {
        return $this->created_at;
    }

    // Getter - Get updated at
    public function getUpdatedAt(): string {
        return $this->updated_at;
    }

    /**
     * Setter
     */
    // Setter - Set Area
    public function setArea(string $area): bool {
        // ToDo: make it proof
        if (
            $area === Application::AREA_HOME 
            || $area === Application::AREA_FIELD 
            || $area === Application::AREA_GOAL
        ) {
            //$this->storeArea($this->game_id, $this->user_id, $this->figure_index, $area);
            $this->area = $area;
            return true;
        }
        return false;
    }

    // Setter - Set Position
    public function setPosition($position): void {
        // ToDo: make it proof
        // find first empty slot in home area an position figure there
        //$this->storePosition($this->game_id, $this->user_id, $this->figure_index, $position);
        $this->position = $position;
    }

    // Helper
    // Helper - Find first empty slot in home area
    public function findFirstEmptySlotInHome(): int {
        // ToDo: Implement
        return 0;
    }
    // Helper - Find empty slots in goal area
    public function findEmptySlotsInGoal(): int {
        // ToDo: Implement
        return 0;
    }

    // Helper - Convert db rows to GameModel dynamically
    private static function fromArrayDynamic(array $row): self {
        $game_state_figure = new self();

        foreach ($row as $key => $value) {
            $game_state_figure->{$key} = $value; 
        }
        return $game_state_figure;
    }

    // Helper - Convert db rows to GameStateFigureModel strict
    public static function fromArray(array $row) : self {
        $game_state_figure = new self();

        $game_state_figure->game_id = self::hydrateString($row, Application::GAME_ID);
        $game_state_figure->user_id = self::hydrateString($row, Application::USER_ID);
        if (array_key_exists(Application::USERNAME, $row))  $game_state_figure->user_name = $row[Application::USERNAME];
        $game_state_figure->figure_index = self::hydrateInt($row, Application::FIGURE_INDEX);
        $game_state_figure->position = self::hydrateInt($row, Application::POSITION);
        $game_state_figure->area = self::hydrateString($row, Application::AREA);
        $game_state_figure->created_at = $row[Application::CREATED_AT];
        $game_state_figure->updated_at = $row[Application::UPDATED_AT];

        return $game_state_figure;
    }

    // Helper - Create Array from GameStateFigureModel
    private function toArray(): array {
        $game_state_figure[Application::GAME_ID] = $this->game_id;
        $game_state_figure[Application::USER_ID] = $this->user_id;
        $game_state_figure[Application::FIGURE_INDEX] = $this->figure_index;
        $game_state_figure[Application::POSITION] = $this->position;
        $game_state_figure[Application::AREA] = $this->area;

        return $game_state_figure;
    }
}