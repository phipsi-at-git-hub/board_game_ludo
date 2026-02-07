<?php
// src/Domain/Game/Game.php
namespace App\Domain\Game;

use App\Domain\Game\GameStatus;
use App\Domain\Game\Player\Player;
use App\Domain\Game\Rules\GameRules;
use InvalidArgumentException;

final class Game {
    private string $id;
    private int $creator_user_id;
    private GameRules $rules;
    private GameStatus $status;

    /** @var Player[] */
    private $players = [];

    private function __construct(string $id, /* int $creator_user_id, */ GameRules $rules) {
        $this->id = $id;
        //$this->creator_user_id = $creator_user_id;
        $this->rules = $rules;
        $this->status = GameStatus::WAITING;
    }

    // Factory method – the ONLY way to create a Game
    /*
    public static function create(int $creator_user_id, GameRules $rules): self {
        return new self(
            id: self::generateId(),
            creator_user_id: $creator_user_id,
            rules: $rules,
            status: GameStatus::WAITING
        );
    }
    */
    public static function create(GameRules $rules): self {
        return new self(
            id: self::generateId(), 
            rules: $rules,
        );
    }

    private static function generateId(): string {
        return bin2hex(random_bytes(16));
    }

    // Player management
    public function addPlayer(Player $player): void {
        if ($this->status !== GameStatus::WAITING) {
            throw new InvalidArgumentException('Cannot join a running game');
        }

        if (count($this->players) >= $this->rules->getMaxPlayers()) {
            throw new InvalidArgumentException('Game is full');
        }
        $this->players[] = $player;
    }

    // State transitions
    public function start(): void {
        if ($this->status !== GameStatus::WAITING) {
            throw new InvalidArgumentException('Game already started');
        }

        if (count($this->players) < $this->rules->getMinPlayers()) {
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

    public function getRules(): GameRules {
        return $this->rules;
    }

    public function getStatus(): GameStatus {
        return $this->status;
    }
}
