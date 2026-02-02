<?php
// Router.php
namespace App\Core;

use Uri\Rfc3986\Uri;

class Router {
    private array $routes = [];

    public function get(string $path, callable|array $action, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $action, $middleware);
    }

    public function post(string $path, callable|array $action, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $action, $middleware);
    }

    public function put(string $path, callable|array $action, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $action, $middleware);
    }

    public function delete(string $path, callable|array $action, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $action, $middleware);
    }

    private function addRoute(string $method, string $path, callable|array $action, array $middleware = []): void
    {
        $this->routes[$method][$path] = [
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // POST-Override für PUT/DELETE aus Formularen
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            echo "404 - Route not found";
            return;
        }

        $route = $this->routes[$method][$uri];

        // Middleware ausführen
        foreach ($route['middleware'] as $mw) {
            $mw();
        }

        // Controller Action aufrufen
        $action = $route['action'];
        if (is_array($action)) {
            [$controllerClass, $methodName] = $action;
            $controller = new $controllerClass();
            $controller->$methodName();
        } else {
            $action();
        }
    }
    /*
    private array $routes = [];

    // Register routes
    public function get(string $path, callable $action) : void {
        $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, callable $action): void {
        $this->addRoute('POST', $path, $action);
    }

    public function put(string $path, callable $action): void {
        $this->addRoute('PUT', $path, $action);
    }

    public function delete(string $path, callable $action): void {
        $this->addRoute('DELETE', $path, $action);
    }

    private function addRoute(string $method, string $path, callable $action): void {
        $this->routes[$method][$path] = $action;
    }

    // Dispatch request
    public function dispatch(): void {
        // Identify HTTP-Method
        $method = $_SERVER['REQUEST_METHOD'];

        // Handle PUT / DELETE via POST-Form-Override
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (isset($this->routes[$method][$uri])) {
            $action = $this->routes[$method][$uri];

            // Check Middleware
            if (is_array($action) && isset($action['middleware'])) {
                foreach ($action['middleware'] as $middleware) {
                    $mw = new $middleware();
                    $mw->handle();
                }
                $action = $action['action'];
            }

            call_user_func($action);
            return;
        }

        http_response_code(404);
        echo "404 Not Found";
    }
    */

    /*
    public function get(string $path, array $handler, array $middlewares = []): void {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array $handler, array $middlewares = []): void {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, array $handler, array $middlewares = []): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->normalize($path),
            'handler' => $handler,
            'middlewares' => $middlewares, 
        ];
    }

    public function dispatch(string $uri, string $method): void {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = $this->normalize($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route['path']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                // Execute Middleware
                foreach ($route['middlewares'] as $middleware) {
                    $middleware();
                }

                [$class, $methodName] = $route['handler'];
                (new $class())->$methodName(...$matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
    */

    private function normalize(string $path): string {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
