<?php 
// src/Domain/Game/State/PlayerState.php
namespace App\Domain\Game\State;

final class PlayerState {
    private readonly string $id;
    private readonly bool $is_bot;

    /** @var FigureState[] */
    private readonly array $figures;

    /** @param FigureState[] $figures */
    public function __construct(string $id, bool $is_bot, array $figures) {
        $this->id = $id;
        $this->is_bot = $is_bot;
        $this->figures = $figures;
    }

    public static function initial(string $id, bool $is_bot = false): self {
        $figures = [];

        for ($i = 0; $i < 4; $i++) {
            $figures[] = FigureState::initial($i, $id);
        }

        return new self(
            id: $id, 
            is_bot: $is_bot, 
            figures: $figures,
        );
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function isBot() : bool {
        return $this->is_bot;
    }

    /** @return FigureState[] */
    public function getFigures(): array {
        return $this->figures;
    }
}