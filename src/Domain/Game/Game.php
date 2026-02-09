<?php
// src/Domain/Game/Game.php
namespace App\Domain\Game;

use App\Domain\Game\GameStatus;
use App\Domain\Game\Player\Player;
use App\Domain\Game\Rules\GameRules;
use App\Domain\Game\State\GameState;
use InvalidArgumentException;

final class Game {
    public const MIN_PLAYERS = 2;
    public const MAX_PLAYER = 4;

    private string $id;
    private string $created_by_user_id;
    private GameStatus $status;
    private GameRules $rules;
    private GameState $state;

    /** @var Player[] */
    private $players = [];

    private function __construct(string $id, string $created_by_user_id, GameStatus $status, GameRules $rules, GameState $state) {
        $this->id = $id;
        $this->created_by_user_id = $created_by_user_id;
        $this->status = $status;
        $this->rules = $rules;
        $this->state = $state;
    }

    // Factory method – the ONLY way to create a Game
    public static function create(string $created_by_user_id, GameRules $rules): self {
        return new self(
            id: self::generateId(), 
            created_by_user_id: $created_by_user_id, 
            status: GameStatus::WAITING,
            rules: $rules,
            state: GameState::initial($rules), 
        );
    }

    public static function restore(string $id, string $created_by_user_id, GameStatus $status, GameRules $rules, GameState $state): self {
        return new self($id, $created_by_user_id, $status, $rules, $state);
    }

    private static function generateId(): string {
        return bin2hex(random_bytes(16));
    }

    // Player management
    public function addPlayer(Player $player): void {
        if ($this->status !== GameStatus::WAITING) {
            throw new InvalidArgumentException('Cannot join a running game');
        }

        if (count($this->players) >= self::MAX_PLAYER) {
            throw new InvalidArgumentException('Game is full');
        }
        $this->players[] = $player;
    }

    // State transitions
    public function start(): void {
        if ($this->status !== GameStatus::WAITING) {
            throw new InvalidArgumentException('Game already started');
        }

        if (count($this->players) < self::MIN_PLAYERS) {
            throw new InvalidArgumentException('Not enough players');
        }

        $this->status = GameStatus::ACTIVE;
    }

    public function finish(): void {
        if ($this->status !== GameStatus::ACTIVE) {
            throw new InvalidArgumentException('Game is not active');
        }

        $this->status = GameStatus::FINISHED;
    }

    // Getters 
    public function getId(): string {
        return $this->id;
    }

    public function getCreatedByUserId(): string {
        return $this->created_by_user_id;
    }

    public function getRules(): GameRules {
        return $this->rules;
    }

    public function getStatus(): GameStatus {
        return $this->status;
    }

    public function getState(): GameState {
        return $this->state;
    }
}
