<?php
// src/Domain/Game/GameState.php
namespace App\Domain\Game;

use App\Domain\Game\Rules\GameRules;
use App\Domain\Game\State\GameStateKey;

final class GameState {
    private array $players;
    private array $figures;
    private int $current_player_index;

    public function __construct(array $players, array $figures, int $current_player_index) {
        $this->players = $players;
        $this->figures = $figures;
        $this->current_player_index = $current_player_index;
    }

    public static function initial(GameRules $rules): self {
        return new self(
            players: [], 
            figures: [], 
            current_player_index: 0, 
        );
    }

    public function toArray(): array {
        return [
            GameStateKey::PLAYERS => $this->players, 
            GameStateKey::Figures => $this->figures, 
            GameStateKey::CURRENT_PLAYER_INDEX => $this->current_player_index, 
        ];
    }

    public static function fromArray(array $data): self {
        return new self(
            $data[GameStateKey::PLAYERS] ?? [], 
            $data[GameStateKey::Figures] ?? [], 
            $data[GameStateKey::CURRENT_PLAYER_INDEX] , 
        );
    }

    // Getters
    public function getCurrentPlayerIndex(): int {
        return $this->current_player_index;
    }

    public function getPlayers(): array {
        return $this->players;
    }

    public function getFigures(): array {
        return $this->figures;
    }
}