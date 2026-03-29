<?php
// AccountController.php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Models\UserModel;

class AccountController extends BaseController {
    // Show profile
    public function profile() {
        //require __DIR__ . '/../Views/account/profile.php';
        $this->render(
            'account/profile', 
            []
        ); 
    }

    // Update profile
    public function updateProfile() {
        $user = Auth::user();
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';

        $user->updateProfile($username, $email);

        header('Location: /account');
        exit;
    }

    // Change password
    public function changePassword() {
        $user = Auth::user();
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (!$user->verifyPassword($current_password)) {
            die('Current password is incorrect.');
        }

        if ($new_password !== $confirm_password) {
            die('New passwords do not match.');
        }

        $user->updatePassword($new_password);

        header('Location: /account');
        exit;
    }

    // Delete account
    public function deleteAccount() {
        $user = Auth::user();
        $user->delete();

        Auth::logout();
        header('Location: /');
        exit;
    }

    // Reset password
    public function showForgotPassword() {
        $this->render(
            'account/forgot_password',
            []
        );
    }

    // Reset password - Send link
    public function sendResetLink() {
        $email = $_POST['email'] ?? '';
        $token = UserModel::createPasswordResetToken($email);

        // Send Email for password reset
        // ToDo
        echo "Reset link: http://localhost:8080/reset-password/$token";
    }

    // Reset password - Reset form
    public function showResetForm(string $token) {
        $this->render(
            'account/reset_password', 
            []
        );
    }

    // Reset password - reset password
    public function resetPassword(string $token) {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($new_password !== $confirm_password) {
            die('Passwords do not match.');
        }

        $user = UserModel::findByResetToken($token);
        if (!$user) {
            die('Invalid or expired token.');
        }

        $user->updatePassword($new_password);
        $user->clearResetToken();

        header('Location: /login');
        exit;
    }
}