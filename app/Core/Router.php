<?php
declare(strict_types=1);

namespace App\Core;

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class Router {
    public static function dispatch(array $routes): void {
        $dispatcher = simpleDispatcher(function(RouteCollector $r) use ($routes) {

            foreach ($routes as $route) {

                [$method, $path, $handler] = $route;

                $r->addRoute($method, $path, $handler);
            }
        });

        $httpMethod = $_SERVER['REQUEST_METHOD'];

        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {

            case \FastRoute\Dispatcher::NOT_FOUND:
                http_response_code(404);
                (new \App\Controllers\PageController())->notFound();
                break;

            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                http_response_code(405);
                (new \App\Controllers\PageController())->notAllowed();
                break;

            case \FastRoute\Dispatcher::FOUND:

                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                // Handler must be an array
                if (!is_array($handler) || count($handler) < 2) {
                    http_response_code(500);
                    (new \App\Controllers\PageController())->notFound();
                    return;
                }

                $class = $handler[0];
                $method = $handler[1];

                // Extra arguments after controller + method
                $extraArgs = array_slice($handler, 2);

                // Safety check
                if (!class_exists($class) || !method_exists($class, $method)) {
                    http_response_code(500);
                    (new \App\Controllers\PageController())->notFound();
                    return;
                }

                $controller = new $class();

                // Merge dynamic URL vars with extra route arguments
                $params = array_merge(array_values($vars), $extraArgs);

                call_user_func_array([$controller, $method], $params);

                break;
        }
    }
}
