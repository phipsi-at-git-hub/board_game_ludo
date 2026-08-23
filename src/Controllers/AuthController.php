<?php
// AuthController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Logging\Logger;
use App\Models\UserModel;
use App\Services\SystemService;

class AuthController extends BaseController {
    public function showLogin() {
        $this->render(
            'auth/login', 
            []
        );
    }

    public function showRegister() {
        $this->render(
            'auth/register', 
            []);
    }

    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = UserModel::verify($email, $password);
        $systemService = $this->app->resolve(SystemService::class); 

        if ($user && $user->isActive() && ($systemService->isLoginEnabled() || $user->isAdmin())
        ) {
            $user->updateLastLogin();
            Auth::login($user);

            // Logging
            Logger::app()->info('User login successful', ['user_id' => $user->getId()]);

            header('Location: /lobby');
            exit;
        }

        $error = (!$systemService->isLoginEnabled() && $user) ? 'Login not allowed.' : 'Invalid login credentials.';
        $this->render(
            'auth/login', 
            [
                'error' => $error
            ]
        );
    }

    public function register() {
        $systemService = $this->app->resolve(SystemService::class); 
        if (!$systemService->isRegistrationEnabled()) {
            $error = 'User Registration not allowed.';
            $this->render(
                'auth/register', 
                [
                    'error' => $error
                ]
            );
            return; 
        }

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = UserModel::create($username, $email, $password);
        $user->updateLastLogin(); 
        Auth::login($user);

        // Logging
        Logger::app()->info('User registration successful', ['user_id' => $user->getId()]);

        header('Location: /lobby');
        exit;
    }

    public function logout() {
        $user = Auth::user(); 
        Auth::logout();

        // Logging
        Logger::app()->info('User logout successful', ['user_id' => $user->getId()]);

        header('Location: /login');
        exit;
    }
}