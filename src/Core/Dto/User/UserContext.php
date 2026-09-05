<?php
// src/Core/Dto/User/UserContext.php

namespace App\Core\Dto\User;

use App\Core\Localization;
use App\Models\User\UserModel;

final class UserContext {
    private static function create(): array {
        return [
            'user_id' => null,
            'username' => null,
            'email' => null,

            'preferred_language' => null,
            'preferred_camera_mode' => null,

            'preferred_language_label' => null,
            'preferred_camera_mode_label' => null,

            'role' => null,
            'status' => null,
        ];
    }

    public static function fromUser(UserModel $user): array {
        $dto = self::create();

        $dto['user_id'] = $user->getId();
        $dto['username'] = $user->getUsername();
        $dto['email'] = $user->getEmail();

        $dto['preferred_language'] = $user->getPreferredLanguage();
        $dto['preferred_camera_mode'] = $user->getPreferredCameraMode();

        $dto['preferred_language_label'] = strtoupper(Localization::get('application.languages.' . $user->getPreferredLanguage()));
        $dto['preferred_camera_mode_label'] = strtoupper(Localization::get('application.camera_mode.' . $user->getPreferredCameraMode()));

        $dto['role'] = $user->getRole();
        $dto['status'] = $user->getStatus();

        return $dto;
    }
}
