<?php 
// src/Constants/User/UserConstants.php 

namespace App\Constants\User;

use App\Constants\Database\DbColumnConstants;

final class UserConstants
{
    /**
     * Defines user statistics constants by database columns.
     */
    public const LOGINS = DbColumnConstants::LOGINS;
    public const GAMES_CREATED = DbColumnConstants::GAMES_CREATED;
    public const GAMES_PARTICIPATED = DbColumnConstants::GAMES_PARTICIPATED;
    public const GAMES_WON = DbColumnConstants::GAMES_WON;
}
