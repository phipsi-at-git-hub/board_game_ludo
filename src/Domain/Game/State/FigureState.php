<?php 
// src/Domain/Game/State/FigureState.php
namespace App\Domain\Game\State;

use App\Domain\Game\Figure;

final class FigureState {
    private int $figure_index;
    private string $player_id;
    private readonly int $position;
    private readonly FigureArea $area;

    public function __construct(int $figure_index, string $player_id, int $position, FigureArea $area) {
        $this->figure_index = $figure_index;
        $this->player_id = $player_id;
        $this->position = $position;
        $this->area = $area;
    }

    public static function initial(int $figure_index, string $player_id): self {
        return new self(
            figure_index: $figure_index, 
            player_id: $player_id, 
            position: -1, 
            area: FigureArea::HOME
        );
    }

    // Getters
    public function getFigureIndex(): int {
        return $this->figure_index;
    }

    public function getPlayerId(): string {
        return $this->player_id;
    }

    public function getPosition(): int {
        return $this->position;
    }
    
    public function getArea(): FigureArea {
        return $this->area;
    }

    // Helpers
    public function isInHome(): bool {
        return $this->area === FigureArea::HOME;
    }

    public function isOnBoard(): bool {
        return $this->area === FigureArea::BOARD;
    }

    public function isInGoal(): bool {
        return $this->area === FigureArea::GOAL;
    }
}