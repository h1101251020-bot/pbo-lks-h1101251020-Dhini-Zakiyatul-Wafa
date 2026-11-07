<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, callable $callback): void
    {
        $this->addRoute('GET', $uri, $callback);
    }

    public function post(string $uri, callable $callback): void
    {
        $this->addRoute('POST', $uri, $callback);
    }

    public function put(string $uri, callable $callback): void
    {
        $this->addRoute('PUT', $uri, $callback);
    }

    public function delete(string $uri, callable $callback): void
    {
        $this->addRoute('DELETE', $uri, $callback);
    }

    private function addRoute(
        string $method,
        string $uri,
        callable $callback
    ): void {
        $this->routes[$method][] = [
            'uri' => $uri,
            'callback' => $callback
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach ($this->routes[$method] ?? [] as $route) {
            $routeUri = $route['uri'];
            $parameterNames = [];

            $segments = explode('/', trim($routeUri, '/'));
            $patternSegments = [];

            foreach ($segments as $segment) {
                if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $segment, $matches)) {
                    $parameterNames[] = $matches[1];
                    $patternSegments[] = '([^/]+)';
                } else {
                    $patternSegments[] = preg_quote($segment, '#');
                }
            }

            $pattern = '#^/' . implode('/', $patternSegments) . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                call_user_func_array(
                    $route['callback'],
                    $matches
                );

                return;
            }
        }

        Response::json([
            'success' => false,
            'message' => "Endpoint tidak ditemukan: $uri"
        ], 404);
    }
}