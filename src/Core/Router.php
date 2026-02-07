<?php
// Router.php
namespace App\Core;

use \App\Core\Debug;

class Router {
    private array $routes = [];

    // Standard HTTP
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

    // Group multiple routes
    public function group(string $prefix, callable $callback, array $middlewares = []): void {
        $proxy = new class($this, rtrim($prefix, '/'), $middlewares) {
            private Router $router;
            private string $prefix;
            private array $middlewares;

            public function __construct(Router $router, string $prefix, array $middlewares) {
                $this->router = $router;
                $this->prefix = $prefix;
                $this->middlewares = $middlewares;
            }

            public function get(string $path, callable|array $action, array $routeMiddleware = []): void {
                $this->router->get($this->prefix . $path, $action, array_merge($this->middlewares, $routeMiddleware));
            }

            public function post(string $path, callable|array $action, array $routeMiddleware = []): void {
                $this->router->post($this->prefix . $path, $action, array_merge($this->middlewares, $routeMiddleware));
            }

            public function put(string $path, callable|array $action, array $routeMiddleware = []): void {
                $this->router->put($this->prefix . $path, $action, array_merge($this->middlewares, $routeMiddleware));
            }

            public function delete(string $path, callable|array $action, array $routeMiddleware = []): void {
                $this->router->delete($this->prefix . $path, $action, array_merge($this->middlewares, $routeMiddleware));
            }
        };

        $callback($proxy);
    }

    // Add interne method
    private function addRoute(string $method, string $path, callable|array $action, array $middleware = []): void {
        $path = $this->normalize($path);
        $this->routes[$method][$path] = [
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    // Dispatch – Execute route
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $routeFound = false;
        foreach ($this->routes[$method] ?? [] as $path => $route) {
            //$pattern = preg_replace('#\{[\w]+\}#', '([\w-]+)', $path);
            $pattern = preg_replace('#\{id\}#', '([0-9a-f]{32})', $path);
            //$pattern = "#^" . $pattern . "$#";
            $pattern = "#^" . rtrim($pattern, '/') . "/?$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); 
                $routeFound = true;

                // Execute Middleware
                foreach ($route['middleware'] as $mw) {
                    $mw();
                }

                // Call Controller action
                $action = $route['action'];
                if (is_array($action)) {
                    [$controllerClass, $methodName] = $action;
                    $controller = new $controllerClass();
                    $controller->$methodName(...$matches); 

                    // Debug in DEV
                    Debug::setRoute($method, $path, get_class($controller) . '@' . $methodName, $route['middleware']);
                } else {
                    $action(...$matches);

                    // Debug in DEV
                    Debug::setRoute($method, $path, 'Closure', $route['middleware']);
                }

                break;
            }

            // Debug in DEV
            Debug::set('route', $path);
            Debug::set('method', $method);
            Debug::set('uri', $uri);
        }

        if (!$routeFound) {
            http_response_code(404);
            echo "404 - Route not found";
        }
    }

    // Normalize paths
    private function normalize(string $path): string {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
