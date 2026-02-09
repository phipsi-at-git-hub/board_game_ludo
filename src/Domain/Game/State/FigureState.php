<?php 
// src/Domain/Game/State/FigureState.php
namespace App\Domain\Game\State;

final class FigureState {
    private readonly FigureArea $area;
    private readonly int $position;

    public function __construct(FigureArea $area, int $position) {
        $this->area = $area;
        $this->position = $position;
    }

    public static function initial(): self {
        return new self(
            FigureArea::HOME, 
            0
        );
    }

    // Getters
    public function getArea(): FigureArea {
        return $this->area;
    }

    public function getPosition(): int {
        return $this->position;
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