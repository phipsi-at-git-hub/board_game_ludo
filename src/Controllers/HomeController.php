<?php 
// HomeController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;

class HomeController extends BaseController {
    public function index() {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        require __DIR__ . '/../Views/home.php';
    }
}
