<?php
// UserRole.php
namespace App\Domain;

final class UserRole {
    public const ADMIN = 'admin'; 
    public const MODERATOR = 'moderator';
    public const GAME_MASTER = 'game_master';

    public static function all(): array {
        return [
            self::ADMIN, 
            self::MODERATOR, 
            self::GAME_MASTER, 
        ];
    }
}