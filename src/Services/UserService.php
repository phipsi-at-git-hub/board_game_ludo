<?php

// src/Services/UserService.php

namespace App\Services;

use App\Constants\Application;
use App\Core\Auth;
use App\Models\User\UserModel;

final class UserService {
    /**
     * Update user
     *
     * Updates the supplied user with the given data.
     *
     * @param UserModel $user
     * @param array $data
     * @return bool
     */
    public function update(UserModel $user, array $data): bool {
        $user->setUsername($data['username'] ?? $user->getUsername());
        $user->setEmail($data['email'] ?? $user->getEmail());
        $user->setRole($data['role'] ?? $user->getRole());
        $user->setStatus($data['status'] ?? $user->getStatus());
        $user->setPreferredLanguage($data['preferred_language'] ?? $user->getPreferredLanguage());
        $user->setPreferredCameraMode($data['preferred_camera_mode'] ?? $user->getPreferredCameraMode());
        if (!$user->save()) {
            return false; 
        }

        // Synchronize session locale when the current user updates their own profile
        $current_user = Auth::user(); 
        if ($current_user && $current_user->getId() === $user->getId()) {
            $_SESSION[Application::LOCALE] = $user->getPreferredLanguage();
        }

        return true;
    } 
}
