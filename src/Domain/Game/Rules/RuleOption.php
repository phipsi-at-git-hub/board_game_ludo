<?php
// src/Domain/Game/Rules/RuleOption.php
namespace App\Domain\Game\Rules;

class RuleOption {
    public function __construct(
        public string $key, 
        public RuleType $type, // bool | int | select
        public mixed $default, 
        public ?array $choices = null, 
        public ?string $label_key, 
        public ?string $description_key = null
    ) {}
}