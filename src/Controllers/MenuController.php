<?php
// MenuController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;

class MenuController extends BaseController {
    public function index() {
        $user = Auth::user();
        require __DIR__ . '/../Views/menu/index.php';
    }
}