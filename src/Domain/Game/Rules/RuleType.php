<?php 
// src/Domain/Game/Rules/RuleType.php
namespace App\Domain\Game\Rules;


enum RuleType: string {
    case BOOL = 'bool';
    case INT = 'int'; 
    case SELECT = 'select';

    public function validate(mixed $value, ?array $choices = null): bool {
        return match($this) {
            self::BOOL => is_bool($value), 
            self::INT => is_int($value), 
            self::SELECT => is_array($value, $choices ?? [], true), 
        };
    }
}
