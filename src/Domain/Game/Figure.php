<?php
// src /Domain/Game/Figure.php
namespace App\Domain\Game;

use App\Domain\Game\State\FigureState;

final class Figure {
    private readonly string $id;
    private readonly string $player_id;
    private FigureState $state;

    public function __construct(string $id, string $player_id, FigureState $state) {
        $this->id = $id;
        $this->player_id = $player_id;
        $this->state = $state;
    }

    public static function createForPlayer(string $player_id, int $index): self {
        return new self(
            id: $index, 
            player_id: $player_id, 
            state: FigureState::initial(), 
        );
    }

    // Getters
    public function getId(): string {
        return $this->id;
    }

    public function getPlayerId(): string {
        return $this->player_id;
    }

    public function getState(): FigureState {
        return $this->state;
    }

    // State mutation
    public function moveTo(FigureState $new_state): void {
        $this->state = $new_state;
    }
}