<?php 
// src/Domain/Game/Persistence/GameSnapshot.php
namespace App\Domain\Game\Persistence;

final class GameSnapshot {
    private readonly string $id;
    private readonly string $status;
    private readonly array $rules;
    private readonly array $state;

    public function __construct(string $id, string $status, array $rules, array $state) {
        $this->id = $id;
        $this->status = $status;
        $this->rules = $rules;
        $this->state = $state;
    }

    // Getters
    public function getId(): string {
        return $this->id;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getRules(): array {
        return $this->rules;
    }

    public function getState(): array {
        return $this->state;
    }
}