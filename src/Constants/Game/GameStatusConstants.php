<?php 
// src/Constants/Game/GameStatusConstants.php 

namespace App\Constants\Game; 

final class GameStatusConstants 
{
    /**
     * Defines game lifecycle statuses.
     */
    public const WAITING = 'WAITING';
    public const RUNNING = 'RUNNING';
    public const FINISHED = 'FINISHED';
    public const CANCELLED = 'CANCELLED';
}