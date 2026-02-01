<?php 
// HomeController.php
namespace App\Controllers;

use App\Core\Auth;

class HomeController {
    public function index() {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        require __DIR__ . '/../Views/home.php';
    }
}
