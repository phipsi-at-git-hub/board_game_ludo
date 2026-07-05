<?php
// src/Core/BaseController.php
namespace App\Core;

use App\Core\Auth;

abstract class BaseController {
    // Renderer
    protected function render(string $view, array $data = [], string $page_title = '', array $css_array = [], array $js_array = []): void {
        if (!$view) {
            return;
        }

        // Prepare variables
        if (!$page_title) {
            $page_title = htmlspecialchars(Localization::get('application.general.title'));
        }

        $data['current_user'] = null;
        
        if (Auth::user()) {
            $data['current_user'] = Auth::user();
        }

        extract($data);

        // Create View and render it
        ob_start();
        require VIEWS_PATH . '/' . $view . '.php';
        $content = ob_get_clean();

        require VIEWS_PATH . '/layout.php';
    }

    // Redirect
    protected function redirect(string $url): void {
        header("Location: $url");
        exit;
    }

    // JSON
    // ToDo: Clean this up and only use jsonClean in the future
    protected function json(array $data): void {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function jsonClean(array $data, int $status = 200): void {
        ob_clean();
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}