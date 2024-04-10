<?php
namespace Erick\PhpLoginApi\app\routes;

use Erick\PhpLoginApi\app\helpers\Request;
use Erick\PhpLoginApi\app\helpers\Uri;

class Routes {
  public const CONTROLLER_NAMESPACE = 'Erick\PhpLoginApi\app\controllers\\';

  static function load(string $controller, string $action) {
    $controllerClass = self::CONTROLLER_NAMESPACE . $controller;

    if(!class_exists($controllerClass)) {
     throw new \Exception('Controller ' . $controller . ' not found', 404);
    }

    $controllerInstance = new $controllerClass();

    if (!method_exists($controllerInstance, $action)) {
      throw new \Exception('Action ' . $action . ' not found', 404);
    }

    $controller = new $controllerClass();
    $controller->$action();
  }

  public static function getRoutes() {

    $routes = [
      'POST' => [
        '/login' => fn() => self::load('LoginController', 'login'),
        '/register' => fn() => self::load('RegisterController', 'register'),
        '/users' => fn() => self::load('UserController', 'index')
      ],
      'GET' => [
        '/' => fn() => self::load('HomeController', 'index'),
        '/users' => self::load('UserController', 'show')
        ]
      ];

    return $routes;
  }

  public static function execute() {
    try {
      $routes = self::getRoutes();
      $method = Request::get();
      $path = Uri::get('path');

      echo $method . ' ' . $path;

      if(!isset($routes[$method])) {
        throw new \Exception('Route Method not found in its API', 404);
      }

      if(!isset($routes[$method][$path])) {
        throw new \Exception('Route not found', 404);
      }



      $routes[$method][$path]();

    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

}
