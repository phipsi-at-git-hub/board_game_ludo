<?php 
// src/Domain/Game/Persistence/GameSnapshotKey.php
namespace App\Domain\Game\Persistence;

final class GameSnapshotKey {
    public const GAMES = 'games';
    public const ID = 'id'; 
    public const CREATED_BY_USER_ID = 'created_by_user_id'; 
    public const STATUS = 'status'; 
    public const RULES = 'rules'; 
    public const STATE = 'state';
}