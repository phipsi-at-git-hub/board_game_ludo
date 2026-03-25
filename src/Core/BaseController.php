<?php
// src/Core/BaseController.php
namespace App\Core;

use App\Core\Auth;

abstract class BaseController {
    // Renderer
    protected function render(string $view, array $data = []): void {
        $data['current_user'] = Auth::user();

        extract($data);

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