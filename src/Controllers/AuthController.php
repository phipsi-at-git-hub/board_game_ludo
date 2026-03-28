<?php
// AuthController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController {
    public function showLogin() {
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function showRegister() {
        require __DIR__ . '/../Views/auth/register.php';
    }

    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = UserModel::verify($email, $password);

        if ($user) {
            Auth::login($user);
            header('Location: /lobby');
            exit;
        }

        $error = 'Invalid login credentials.';
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function register() {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = UserModel::create($username, $email, $password);
        Auth::login($user);

        header('Location: /lobby');
        exit;
    }

    public function logout() {
        Auth::logout();
        header('Location: /login');
        exit;
    }
}