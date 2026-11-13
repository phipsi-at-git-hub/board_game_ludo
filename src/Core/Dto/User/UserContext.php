<?php
// src/Core/Dto/User/UserContext.php

namespace App\Core\Dto\User;

use App\Constants\Dto\UserDtoConstants;
use App\Core\Localization;
use App\Models\User\UserModel;

/**
 * UserContext
 */
final readonly class UserContext
{
    public string $user_id; 
    public string $username; 
    public string $email; 

    public string $preferred_language; 
    public string $preferred_language_label; 

    public string $preferred_camera_mode; 
    public string $preferred_camera_mode_label; 

    public string $role; 
    public string $status; 

    /**
     * __construct
     *
     * @param UserModel $user
     * @return void
     */
    public function __construct(UserModel $user)
    {
        $this->user_id = $user->getId(); 
        $this->username = $user->getUsername(); 
        $this->email = $user->getEmail(); 

        $this->preferred_language = $user->getPreferredLanguage(); 
        $this->preferred_language_label = strtoupper(Localization::get('application.languages.' . $user->getPreferredLanguage())); 

        $this->preferred_camera_mode = $user->getPreferredCameraMode(); 
        $this->preferred_camera_mode_label = strtoupper(Localization::get('application.camera_mode.' . $user->getPreferredCameraMode())); 

        $this->role = $user->getRole(); 
        $this->status = $user->getStatus(); 
    }

    /**
     * fromUser
     * 
     * Returns an array for compatibility reasons. 
     *
     * @param UserModel $user
     * @return array
     */
    public static function fromUser(UserModel $user): array
    {
        $context = new self(user: $user); 

        return $context->jsonSerialize(); 
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize(): array {
        return [
            UserDtoConstants::USER_ID => $this->user_id, 
            UserDtoConstants::USERNAME => $this->username, 
            UserDtoConstants::EMAIL => $this->email, 

            UserDtoConstants::PREFERRED_LANGUAGE => $this->preferred_language, 
            UserDtoConstants::PREFERRED_LANGUAGE_LABEL => $this->preferred_language_label, 

            UserDtoConstants::PREFERRED_CAMERA_MODE => $this->preferred_camera_mode, 
            UserDtoConstants::PREFERRED_CAMERA_MODE_LABEL => $this->preferred_camera_mode_label, 

            UserDtoConstants::ROLE => $this->role, 
            UserDtoConstants::STATUS => $this->status, 
        ]; 
    }
}
