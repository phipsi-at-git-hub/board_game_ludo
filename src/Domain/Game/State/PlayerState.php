<?php 
// src/Domain/Game/State/PlayerState.php
namespace App\Domain\Game\State;

final class PlayerState {
    private readonly string $id;
    private readonly array $figures;

    /** @param FigureState[] $figures */
    public function __construct(string $id, array $figures) {
        $this->id = $id;
        $this->figures = $figures;
    }

    public static function initial(string $id): self {
        return new self(
            $id, 
            [
                FigureState::initial(), 
                FigureState::initial(), 
                FigureState::initial(), 
                FigureState::initial(), 
            ],
        );
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getFigures(): array {
        return $this->figures;
    }
}