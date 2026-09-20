<?php


class Router
{
    public static function dispatch(array $routes, string $paramName = 'action', ?string $default = null): void
    {
        $action = $_GET[$paramName] ?? $default;

        if ($action === null || !isset($routes[$action])) {
            http_response_code(404);
            echo "Route inconnue" . ($action ? " : " . htmlspecialchars($action) : "") . ".";
            return;
        }

        call_user_func($routes[$action]);
    }

    public static function url(string $controllerFile, string $action, array $extraParams = []): string
    {
        $params = array_merge(['action' => $action], $extraParams);
        return BASE_URL . 'controllers/' . $controllerFile . '.php?' . http_build_query($params);
    }
}
