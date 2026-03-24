<?php

class Router
{
    private $routes = [];
    private $container = [];

    public function registerController($name, $instance)
    {
        $this->container[$name] = $instance;
    }

    public function get($uri, $callback, $middleware = [])
    {
        $this->addRoute('GET', $uri, $callback, $middleware);
    }

    public function post($uri, $callback, $middleware = [])
    {
        $this->addRoute('POST', $uri, $callback, $middleware);
    }

    public function put($uri, $callback, $middleware = [])
    {
        $this->addRoute('PUT', $uri, $callback, $middleware);
    }

    public function delete($uri, $callback, $middleware = [])
    {
        $this->addRoute('DELETE', $uri, $callback, $middleware);
    }

    private function addRoute($method, $uri, $callback, $middleware)
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([\w-]+)', $uri);
        $pattern = "#^$pattern$#";

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback,
            'middleware' => $middleware
        ];
    }

    public function resolve($requestUri, $requestMethod)
    {
        $uri = trim(parse_url($requestUri, PHP_URL_PATH), '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) continue;

            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);

                foreach ($route['middleware'] as $mw) {
                    if (class_exists($mw)) {
                        $middleware = new $mw();
                        $middleware->handle();
                    }
                }

                if (is_callable($route['callback'])) {
                    return call_user_func_array($route['callback'], $matches);
                }

                list($controllerName, $action) = explode('@', $route['callback']);

                if (!isset($this->container[$controllerName])) {
                    throw new Exception("Controller $controllerName not registered in container.");
                }

                $controller = $this->container[$controllerName];

                return call_user_func_array([$controller, $action], $matches);
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}