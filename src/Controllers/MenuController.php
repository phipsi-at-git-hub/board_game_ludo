<?php
// MenuController.php
namespace App\Controllers;

use App\Core\Auth;

class MenuController {
    public function index() {
        $user = Auth::user();
        require __DIR__ . '/../Views/menu/index.php';
    }
}