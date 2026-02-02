<?php
// Router.php
namespace App\Core;

use Uri\Rfc3986\Uri;

class Router {
    private array $routes = [];

    public function get(string $path, callable|array $action, array $middleware = []): void {
        $this->addRoute('GET', $path, $action, $middleware);
    }

    public function post(string $path, callable|array $action, array $middleware = []): void {
        $this->addRoute('POST', $path, $action, $middleware);
    }

    public function put(string $path, callable|array $action, array $middleware = []): void {
        $this->addRoute('PUT', $path, $action, $middleware);
    }

    public function delete(string $path, callable|array $action, array $middleware = []): void {
        $this->addRoute('DELETE', $path, $action, $middleware);
    }

    private function addRoute(string $method, string $path, callable|array $action, array $middleware = []): void {
        $this->routes[$method][$path] = [
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];

        // POST-Override for PUT/DELETE from forms
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

        // Execute Middleware
        foreach ($route['middleware'] as $mw) {
            $mw();
        }

        // Call Controller action
        $action = $route['action'];
        if (is_array($action)) {
            [$controllerClass, $methodName] = $action;
            $controller = new $controllerClass();
            $controller->$methodName();
        } else {
            $action();
        }
    }

    private function normalize(string $path): string {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
