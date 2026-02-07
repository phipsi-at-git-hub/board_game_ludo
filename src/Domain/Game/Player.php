<?php
// src/Domain/Game/Player/Player.php
namespace App\Domain\Game\Player;

final class Player {
    private string $id;
    private string $name;
    private bool $is_bot; 
    public function __construct(string $id, string $name, bool $is_bot = false) {}

    // Getters
    public function getId(): string {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function isBot(): bool {
        return $this->is_bot;
    }
}