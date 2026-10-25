<?php
// src/Constants/Dto/UserDtoConstants.php

namespace App\Constants\Dto;

final class UserDtoConstants
{
    /**
     * Defines user DTO keys.
     */
    public const USER_ID = 'user_id';
    public const USERNAME = 'username'; 
    public const EMAIL = 'email'; 
    public const PREFERRED_LANGUAGE = 'preferred_language'; 
    public const PREFERRED_CAMERA_MODE = 'preferred_camera_mode'; 
    public const PREFERRED_LANGUAGE_LABEL = self::PREFERRED_LANGUAGE . '_label'; 
    public const PREFERRED_CAMERA_MODE_LABEL = self::PREFERRED_CAMERA_MODE . '_label'; 
    public const ROLE = 'role'; 
    public const STATUS = 'status'; 
}
