<?php
// AccountController.php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Http\Http;
use App\Core\Logging\Logger;
use App\Models\User\UserModel;
use App\Services\MailService;

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
        
        // Logging
        Logger::app()->info('User account - User ' . $user->getUsername() . ' (' . $user->getId() . ') updated', ['user_id' => Auth::user()->getId()]);

        $this->redirect('/account'); 
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
        
        // Logging
        Logger::app()->info('User account - User ' . $user->getUsername() . ' changed password. (' . $user->getId() . ') updated', ['user_id' => Auth::user()->getId()]);

        $this->redirect('/account'); 
    }

    // Delete account
    public function deleteAccount() {
        $user = Auth::user();
        
        // Logging
        Logger::app()->notice('User account - User ' . $user->getUsername() . ' deleted own account. (' . $user->getId() . ') updated', ['user_id' => Auth::user()->getId()]);
        $user->delete();

        Auth::logout();

        $this->redirect('/'); 
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
        $token = UserModel::createPasswordToken($email);
        if ($token === null) {
            $this->redirect('/forgot-password'); 
        }
        
        $user = UserModel::findByEmail($email); 
        if ($user === null) {
            $this->redirect('/forgot-password'); 
        }

        $resetUrl = Http::url('/reset-password/'. $token);

        // Send Email for password reset
        $mailService = new MailService(); 
        $mailService->sendPasswordReset($user, $resetUrl); 

        // Logging
        Logger::app()->debug('Show password reset form.', []);

        $this->render(
            'account/set_password_sent', 
            []
        ); 
    }

    // Reset password - Reset form
    public function showResetForm(string $token) {
        // Logging
        Logger::app()->debug('Show password reset form.', []);

        $this->render(
            'account/set_password', 
            [
                'label' => 'reset', 
            ]
        );
    }

    // Reset password - reset password
    public function resetPassword(string $token) {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($new_password !== $confirm_password) {
            die('Passwords do not match.');
        }

        $user = UserModel::findByPasswordToken($token);
        if (!$user) {
            die('Invalid or expired token.');
        }

        $user->updatePassword($new_password);
        $user->clearPasswordToken();
        
        // Logging
        Logger::app()->info('User account - User ' . $user->getUsername() . ' reset own password.', ['user_id' => $user->getId()]);

        $this->redirect('/login'); 
    }

    // Create user by admin - set password form
    public function createPasswordForm(string $token): void {
        // Logging
        Logger::app()->debug('Show password reset form.', []);

        $this->render(
            'account/set_password', 
            [
                'label' => 'create_user', 
            ]
        );
    }

    // Crate user password - set password
    public function createUserPassword(string $token): void {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($new_password !== $confirm_password) {
            die('Passwords do not match.');
        }

        $user = UserModel::findByPasswordToken($token);
        if (!$user) {
            die('Invalid or expired token.');
        }

        $user->updatePassword($new_password); 
        $user->activate(); 
        $user->clearPasswordToken();
        
        // Logging
        Logger::app()->notice('User account - User ' . $user->getUsername() . ' set own password and activated own account.', ['user_id' => $user->getId()]);

        $this->redirect('/login'); 
    }
}
