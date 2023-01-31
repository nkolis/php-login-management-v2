<?php

namespace App\PHPLoginManagement\Core;

class Router
{
  private static array $routes;

  public static function add(string $method, string $path, string $controller, string $function, array $middleware = []): void
  {
    self::$routes[] = [
      'method' => $method,
      'path' => $path,
      'controller' => $controller,
      'function' => $function,
      'middleware' => $middleware
    ];
  }

  public static function run(): void
  {

    $url = $_SERVER['PATH_INFO'] ?? '/';
    $method = $_SERVER['REQUEST_METHOD'];

    foreach (self::$routes as $route) {
      $path = preg_replace('/\{\*\}/', '([0-9A-Za-z]*)', $route['path']);

      $pattern = '#^' . $path . '$#';
      if (preg_match($pattern, $url, $variables) && $method === $route['method']) {

        foreach ($route['middleware'] as $middleware) {
          $instance = new $middleware;
          $instance->before();
        }

        array_shift($variables);
        $controller = new $route['controller'];

        call_user_func_array([$controller, $route['function']], $variables);
        return;
      }
    }

    http_response_code(404);
    echo "CONTROLLER NOT FOUND";
  }
}
