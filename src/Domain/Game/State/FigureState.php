<?php 
// src/Domain/Game/State/FigureState.php
namespace App\Domain\Game\State;

final class FigureState {
    private readonly int $position;
    private readonly bool $is_home;

    public function __construct(int $position, bool $is_home) {
        $this->position = $position;
        $this->is_home = $is_home;
    }

    public static function initial(): self {
        return new self(-1, true);
    }

    // Getters
    public function getPosition(): int {
        return $this->position;
    }

    public function isHome(): bool {
        return $this->is_home;
    }
}