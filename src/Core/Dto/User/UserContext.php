<?php
// src/Core/Dto/User/UserContext.php

namespace App\Core\Dto\User;

use App\Constants\Dto\UserDtoConstants;
use App\Core\Localization;
use App\Models\User\UserModel;

final class UserContext
{
    private static function create(): array
    {
        return [
            UserDtoConstants::USER_ID => null,
            UserDtoConstants::USERNAME => null,
            UserDtoConstants::EMAIL => null,

            UserDtoConstants::PREFERRED_LANGUAGE => null,
            UserDtoConstants::PREFERRED_CAMERA_MODE => null,

            UserDtoConstants::PREFERRED_LANGUAGE_LABEL => null,
            UserDtoConstants::PREFERRED_CAMERA_MODE_LABEL => null,

            UserDtoConstants::ROLE => null,
            UserDtoConstants::STATUS => null,
        ];
    }

    public static function fromUser(UserModel $user): array
    {
        $dto = self::create();

        $dto[UserDtoConstants::USER_ID] = $user->getId();
        $dto[UserDtoConstants::USERNAME] = $user->getUsername();
        $dto[UserDtoConstants::EMAIL] = $user->getEmail();

        $dto[UserDtoConstants::PREFERRED_LANGUAGE] = $user->getPreferredLanguage();
        $dto[UserDtoConstants::PREFERRED_CAMERA_MODE] = $user->getPreferredCameraMode();

        $dto[UserDtoConstants::PREFERRED_LANGUAGE_LABEL] = strtoupper(Localization::get('application.languages.' . $user->getPreferredLanguage()));
        $dto[UserDtoConstants::PREFERRED_CAMERA_MODE_LABEL] = strtoupper(Localization::get('application.camera_mode.' . $user->getPreferredCameraMode()));

        $dto[UserDtoConstants::ROLE] = $user->getRole();
        $dto[UserDtoConstants::STATUS] = $user->getStatus();

        return $dto;
    }
}
