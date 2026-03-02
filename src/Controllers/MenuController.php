<?php
// MenuController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;

class MenuController extends BaseController {
    public function index() {
        $user = Auth::user();
        $this->render(
            'menu/index', 
            [
                'user' => $user
            ]
        );
        exit;
    }
}