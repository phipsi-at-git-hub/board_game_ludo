<?php
// src/Core/Renderer/ViewRenderer.php

namespace App\Core\Renderer;

use App\Core\Application\App;
use App\Core\Auth\Auth;
use App\Core\Localization;
use App\Services\SystemService;
use RuntimeException;
use Throwable;

final class ViewRenderer
{ 
    /**
     * Dependencies
     */
    private readonly App $app; 

    /**
     * __construct
     *
     * @param App $app
     * @return void
     */
    public function __construct(App $app)
    {
        $this->app = $app; 
    }

    /**
     * Renderer
     */

    /**
     * render
     * 
     * Render a complete page using a structured view path. 
     *
     * @param ?string $structure
     * @param string $view
     * @param array $data
     * @param string $pageTitle
     * @param array $cssArray
     * @param array $jsArray
     * @return void
     */
    public function render(
        ?string $structure, 
        string $view, 
        array $data = [], 
        string $pageTitle = '', 
        array $cssArray = [], 
        array $jsArray = [] 
    ): void {
        $viewPath = $this->resolveStructuredPath($structure, $view); 
        $viewData = $this->prepareData($data, true); 
        $content = $this->renderFile($viewPath,$viewData); 

        $this->renderLayout(
            $content, 
            $viewData, 
            $pageTitle, 
            $cssArray, 
            $jsArray
        ); 
    }

    /**
     * renderString
     * 
     * Render a structured view and return its HTML. 
     *
     * @param ?string $structure
     * @param string $view
     * @param array $data
     * @return string
     */
    public function renderString(
        ?string $structure, 
        string $view, 
        array $data = []
    ): string {
        $path = $this->resolveStructuredPath($structure, $view); 

        return $this->renderFile(
            $path, 
            $this->prepareData($data) 
        ); 
    }

    /**
     * renderPartialString
     * 
     * Render a structured partial and return its HTML. 
     *
     * @param ?string $structure
     * @param string $view
     * @param array $data
     * @return string
     */
    public function renderPartialString (
        ?string $structure, 
        string $view, 
        array $data = [] 
    ): string {
        $path = $this->resolveStructuredPath($structure, $view, true); 

        return $this->renderFile(
            $path, 
            $this->prepareData($data)
        ); 
    }

    /**
     * renderFullPath
     * 
     * Render a complete page using a relative path from VIEWS_PATH. 
     *
     * @param string $view
     * @param array $data
     * @param string $pageTitle
     * @param array $cssArray
     * @param array $jsArray
     * @return void
     */
    public function renderFullPath(
        string $view, 
        array $data = [], 
        string $pageTitle = '', 
        array $cssArray = [], 
        array $jsArray = [] 
    ): void {
        $viewPath = $this->resolveFullPath($view); 
        $viewData = $this->prepareData($data, true); 
        $content = $this->renderFile($viewPath, $viewData); 

        $this->renderLayout(
            $content, 
            $viewData, 
            $pageTitle, 
            $cssArray, 
            $jsArray 
        ); 
    }

    /**
     * renderFullPathString
     * 
     * Render a view using a relative path from VIEWS_PATH. 
     * and return its HTML. 
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public function renderFullPathString(
        string $view, 
        array $data = [] 
    ): string {
        $path = $this->resolveFullPath($view); 

        return $this->renderFile(
            $path, 
            $this->prepareData($data)
        ); 
    }

    /**
     * Private renderer
     */

    /**
     * renderLayout
     * 
     * Render the page layout around the already rendered view. 
     *
     * @param string $content
     * @param array $data
     * @param string $pageTitle
     * @param array $cssArray
     * @param array $jsArray
     * @return void
     */
    private function renderLayout(
        string $content, 
        array $data, 
        string $pageTitle, 
        array $cssArray, 
        array $jsArray
    ): void {
        if ($pageTitle === '') {
            $pageTitle = htmlspecialchars(
                Localization::get('application.general.title'), 
                ENT_QUOTES, 
                'UTF-8' 
            ); 
        }

        $data['content'] = $content; 
        $data['page_title'] = $pageTitle; 
        $data['css_array'] = $cssArray; 
        $data['js_array'] = $jsArray; 

        echo $this->renderFile(
            $this->resolveFullPath('layout'), 
            $data 
        ); 
    }

    /**
     * renderFile
     * 
     * Render a PHP file and return its output. 
     *
     * @param string $filePath
     * @param array $data
     * @return string
     */
    private function renderFile(
        string $filePath, 
        array $data 
    ): string {
        $bufferLevel = ob_get_level(); 
        ob_start(); 

        // ToDo: I don't like throwing stuff in environments like this at all - it should be handled properly 
        try {
            (static function (string $filePath, array $data): void {
                extract($data, EXTR_SKIP); 
                require $filePath; 
            })($filePath, $data); 
            return (string) ob_get_clean(); 
        } catch (Throwable $exception) {
            while (ob_get_level() > $bufferLevel) {
                ob_end_clean(); 
            } 
            throw $exception; 
        }
    }

    /** 
     * Resolving path stuff
     */

    /**
     * resolveStructuredPath
     * 
     * Resolve a structured view or partial path. 
     *
     * @param ?string $structure
     * @param string $view
     * @param bool $partial
     * @return string
     */
    private function resolveStructuredPath(
        ?string $structure, 
        string $view, 
        bool $partial = false
    ): string {
        // If $structure is empty $parts array only uses $view and maybe with 'partials' before that
        $parts = !empty($structure) ? [$structure] : []; 
        if ($partial) {
            $parts[] = 'partials'; 
        } 

        $parts[] = $view; 

        return $this->resolveFullPath(
            implode('/', $parts)
        ); 
    }

    /**
     * resolveFullPath
     * 
     * Resolve and validate a relative path from VIEWS_PATH. 
     *
     * @param string $view
     * @return string
     */
    private function resolveFullPath(string $view): string
    {
        if ($view === '' || str_contains($view, "\0")) {
            // Todo: I don't like these exceptions at all and these should be handled properly 
            throw new RuntimeException('Invalid view path.'); 
        }

        // Normalize path separators 
        $view = str_replace('\\', '/', $view); 

        // Absolute paths and windows drive paths are not allowed 
        if (
            str_starts_with($view, '/') || 
            preg_match('/^[a-zA-Z]:/', $view)
        ) {
            // Todo: I don't like these exceptions at all and these should be handled properly 
            throw new RuntimeException("View path must be relative to VIEWS_PATH."); 
        }

        // Reject directory traversal 
        foreach (explode('/', $view) as $segment) {
            if ($segment === '.' || $segment === '..') {
                // Todo: I don't like these exceptions at all and these should be handled properly 
                throw new RuntimeException('Invalid view path segment.'); 
            }
        }

        // Add the PHP extension when omitted 
        if (!str_ends_with($view, '.php')) {
            $view .= '.php'; 
        }

        // Only PHP view files are supported 
        if (strtolower(pathinfo($view, PATHINFO_EXTENSION)) !== 'php') {
            // Todo: I don't like these exceptions at all and these should be handled properly 
            throw new RuntimeException('Only PHP view files are supported. '); 
        }

        $basePath = realpath(VIEWS_PATH); 

        if ($basePath === false) {
            // Todo: I don't like these exceptions at all and these should be handled properly 
            throw new RuntimeException('Views directory does not exist.'); 
        }

        $resolvedPath = realpath($basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $view)); 

        // Ensure that the resolved file is inside VIEWS_PATH 
        if (
            $resolvedPath === false || 
            !is_file($resolvedPath) || 
            !str_starts_with(
                $resolvedPath, 
                $basePath . DIRECTORY_SEPARATOR
            )
        ) {
            // Todo: I don't like these exceptions at all and these should be handled properly 
            throw new RuntimeException("View not found {$view}"); 
        }

        return $resolvedPath; 
    }

    /**
     * Helper
     */

    /**
     * prepareData
     * 
     * Prepare the data available to a view. 
     *
     * @param array $data
     * @param bool $includeSystemSettings
     * @return array
     */
    private function prepareData(
        array $data, 
        bool $includeSystemSettings = false 
    ): array {
        $data['current_user'] = null; 
        if (Auth::user()) {
            $data['current_user'] = Auth::user(); 
        }

        if ($includeSystemSettings) {
            $data['system_settings'] = $this->app->resolve(SystemService::class); 
        }
        
        $data['viewRenderer'] = $this; 

        return $data; 
    }
}
