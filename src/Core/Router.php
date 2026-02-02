<?php
// Router.php
namespace App\Core;

class Router {
    private array $routes = [];

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

    private function normalize(string $path): string {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
