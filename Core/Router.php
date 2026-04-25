<?php

namespace Core;

class Router {
    protected $routes = [];

    public function add($method, $path, $handler) {
        $path = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $path);
        $this->routes[] = [
            'method' => $method,
            'path' => "#^" . $path . "$#i",
            'handler' => $handler
        ];
    }

    public function dispatch($url, $method) {
        // Remove base path from URL if exists
        $config = require __DIR__ . '/../config/config.php';
        $base_url = rtrim($config['base_url'], '/');
        $base_path = parse_url($base_url, PHP_URL_PATH);
        
        if ($base_path && $base_path !== '/') {
            if (strpos($url, $base_path) === 0) {
                $url = substr($url, strlen($base_path));
            }
        }
        
        $url = trim($url, '/');
        if ($url === '') $url = '/';
        else $url = '/' . $url;

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['path'], $url, $matches)) {
                list($controllerName, $methodName) = explode('@', $route['handler']);
                $controllerName = "App\\Controllers\\" . $controllerName;
                
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $methodName)) {
                        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                        call_user_func_array([$controller, $methodName], $params);
                        return;
                    }
                }
            }
        }

        // 404
        http_response_code(404);
        echo "404 Not Found";
    }
}
