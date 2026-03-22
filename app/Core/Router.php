<?php

class Router
{
    protected array $routes = [];

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function dispatch(string $page, string $action): void
    {
        if (isset($this->routes[$page])) {
            [$controllerName, $method, $roles] = $this->routes[$page];

            // Middleware Role Check
            if (!empty($roles)) {
                Middleware::requireRole($roles);
            }

            // Load controller (check /app/Controllers/ dir)
            $controllerFile = CONTROLLERS_PATH . DS . $controllerName . '.php';
            if (!file_exists($controllerFile)) {
                $this->abort(404);
            }

            require_once $controllerFile;
            $controller = new $controllerName();

            if (!method_exists($controller, $method)) {
                $this->abort(404);
            }

            $controller->$method();
        } else {
            $this->abort(404);
        }
    }

    protected function abort(int $code): void
    {
        http_response_code($code);
        renderView("errors/{$code}");
        exit;
    }
}
