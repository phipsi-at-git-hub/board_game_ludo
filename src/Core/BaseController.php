<?php
// src/Core/BaseController.php
namespace App\Core;

use App\Core\Application\App;
use App\Core\Auth\Auth;
use App\Core\Renderer\ViewRenderer;

/**
 * BaseController
 */
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

    /**
     * render
     *
     * @param string $view
     * @param array $data
     * @param string $pageTitle
     * @param array $cssArray
     * @param array $jsArray
     * @return void
     */
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

    /**
     * renderView
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    protected function renderView(
        string $view, 
        array $data = [] 
    ): string {
        return $this->getViewRenderer()->renderFullPathString(
            $view, 
            $data
        ); 
    }

    /**
     * redirect
     *
     * @param string $url
     * @return void
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    // ToDo: Clean this up and only use jsonClean in the future
    /**
     * json
     *
     * @param array $data
     * @return void
     */
    protected function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * jsonClean
     *
     * @param array $data
     * @param int $status
     * @return void
     */
    protected function jsonClean(array $data, int $status = 200): void
    {
        ob_clean();
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
