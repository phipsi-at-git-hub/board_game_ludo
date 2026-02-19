<?php
// src/Core/BaseController.php
namespace App\Core;

use App\Core\Auth;
use App\Core\Csrf;

abstract class BaseController {
    // Renderer
    protected function render(string $view, array $data = []): void {
        //$data['_csrf_token'] = Csrf::generate();
        $data['current_user'] = Auth::user();

        extract($data);

        ob_start();
        require VIEWS_PATH . '/' . $view . '.php';
        $content = ob_get_clean();

        require VIEWS_PATH . '/layout.php';
    }

    protected function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
}