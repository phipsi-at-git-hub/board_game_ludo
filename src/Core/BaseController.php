<?php
// src/Core/BaseController.php
namespace App\Core;

use App\Core\Application\App;
use App\Core\Auth\Auth;
use App\Core\Renderer\ViewRenderer;
use App\Services\SystemService;

abstract class BaseController
{
    protected App $app;

    private ?ViewRenderer $viewRenderer = null; 

    // Constructor 
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    private function getViewRenderer(): ViewRenderer
    {
        return $this->viewRenderer ??= $this->app->resolve(ViewRenderer::class); 
    }

    protected function render(
        string $view, 
        array $data = [], 
        string $pageTitle = '', 
        array $cssArray = [], 
        array $jsArray = []
    ): void {
        $this->getViewRenderer()->renderFullPath(
            $view, 
            $data, 
            $pageTitle, 
            $cssArray, 
            $jsArray
        ); 
    }

    protected function renderView(
        string $view, 
        array $data = [] 
    ): string {
        return $this->getViewRenderer()->renderFullPathString(
            $view, 
            $data
        ); 
    }

    // Renderer
    protected function render1(string $view, array $data = [], string $page_title = '', array $css_array = [], array $js_array = []): void
    {
        if (!$view) {
            return;
        }

        // Prepare variables
        if (!$page_title) {
            $page_title = htmlspecialchars(Localization::get('application.general.title'));
        }

        // current_user should always be available in views
        $data['current_user'] = null;

        if (Auth::user()) {
            $data['current_user'] = Auth::user();
        }

        // system_settings should always be available in views
        $data['system_settings'] = $this->app->resolve(SystemService::class);

        extract($data);

        // Create View and render it
        ob_start();
        require VIEWS_PATH . '/' . $view . '.php';
        $content = ob_get_clean();

        require VIEWS_PATH . '/layout.php';
    }

    // Renderer for view fragment
    protected function renderView1(string $view, array $data = []): string
    {
        if (!$view) {
            return '';
        }

        $data['current_user'] = null;

        if (Auth::user()) {
            $data['current_user'] = Auth::user();
        }

        extract($data);
        ob_start();
        require VIEWS_PATH . '/' . $view . '.php';
        return ob_get_clean();
    }

    // Redirect
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    // JSON
    // ToDo: Clean this up and only use jsonClean in the future
    protected function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function jsonClean(array $data, int $status = 200): void
    {
        ob_clean();
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
