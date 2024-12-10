<?php

namespace app\router;


class Router
{
    private static ?array $routes = ['GET' => [], 'POST' => [], 'PUT' => [], 'DELETE' => [], 'PATCH' => [], 'OPTIONS' => []];



    private static function addRoute(string $method, string $url, ?callable $handle = null): Route
    {
        $route = new Route($handle);
        self::$routes[$method][$url] = $route;
        return $route;
    }

    public static function __callStatic($name, $arguments)
    {
        $method = strtoupper($name);
        if (in_array($method, array_keys(self::$routes))) {
            return self::addRoute($method, $arguments[0], $arguments[1] ?? null);
        }
        throw new \BadMethodCallException("Method $name does not exist");
    }

    public static function match(array $methods, string $url, callable $handle): Route
    {
        $route = new Route($handle);
        foreach ($methods as $method) {
            if (in_array($method, array_keys(self::$routes))) {
                self::$routes[$method][$url] = $route;
            }
        }
        return $route;
    }

    public static function any(string $url, callable $handle): Route
    {
        $route = new Route($handle);
        foreach (self::$routes as $method => &$routes) {
            $routes[$url] = $route;
        }
        return $route;
    }

    public static function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = $_SERVER['REQUEST_URI'];

        $urlPath = strtok($url, '?');
        $urlPath = strtok($urlPath, '#');

        if (!isset(self::$routes[$method]))
            // header("Location: /404");
        Response::error("page not found", ResponseCode::NOT_FOUND)->send();
        if (!isset(self::$routes[$method][$urlPath]))
            // header("Location: /404");
        Response::error("page not found", ResponseCode::NOT_FOUND)->send();
        else
            self::$routes[$method][$urlPath]->execute();
    }
}
