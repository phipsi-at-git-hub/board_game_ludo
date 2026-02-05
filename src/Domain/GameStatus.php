<?php
// GameStatus.php
namespace App\Domain;

final class GameStatus {
    public const WAITING = 'waiting';
    public const ACTIVE = 'active';
    public const PAUSED = 'paused';
    public const FINISHED = 'finished';
    public const ABORTED = 'aborted';

    public static function all(): array {
        return [
            self::WAITING, 
            self::ACTIVE, 
            self::PAUSED, 
            self::FINISHED, 
            self::ABORTED, 
        ];
    }
}