<?php
namespace Erick\PhpLoginApi\app\routes;

use Erick\PhpLoginApi\app\helpers\Request;
use Erick\PhpLoginApi\app\helpers\Uri;

class Routes {
  public const CONTROLLER_NAMESPACE = 'Erick\PhpLoginApi\app\controllers\\';

  static function load(string $controller, string $action, $params = []) {
    $controllerClass = self::CONTROLLER_NAMESPACE . $controller;

    if(!class_exists($controllerClass)) {
     throw new \Exception('Controller ' . $controller . ' not found', 404);
    }

    $controllerInstance = new $controllerClass();

    if (!method_exists($controllerInstance, $action)) {
      throw new \Exception('Action ' . $action . ' not found', 404);
    }

    // Passa os parâmetros para a ação do controlador
    return call_user_func_array([$controllerInstance, $action], $params);
  }

  public static function getRoutes() {

    $routes = [
      'POST' => [
        '/login' => fn() => self::load('LoginController', 'login'),
        '/document' => fn() => self::load('RegisterController', 'createDocument'),
        '/users' => fn() => self::load('UserController', 'createUser'),
        '/upload' => fn() => self::load('ImageController', 'uploadImage'),
        '/documents/(\d+)' => function($id) { 
          return self::load('DocumentController', 'updateDocument', [$id]);
        }
      ],
      'GET' => [
        '/users' => fn() => self::load('UserController', 'showUsers'),
        // Modificando para capturar o ID dinâmico
        '/users/(\d+)' => function($id) {
          return self::load('UserController', 'retrieve', [$id]);
        },
        '/documents' => function() {
      
        return self::load('DocumentController', 'readByNFKey');
        },
        '/documents/(\d+)' => function($id) {
          return self::load('DocumentController', 'read', [$id]);
        }
      ],
      'PUT' => [
        '/documents/(\d+)' => function($id) {
          return  self::load('DocumentController', 'updateDocument', [$id]);
        },
        '/users/(\d+)' => function($id) {
          return self::load('UserController', 'updateUser', [$id]);
        },
      ],
      'DELETE' => [
        '/users/(\d+)' => function($id) {
          return self::load('UserController', 'deleteUser', [$id]);
        },
      ]
    ];
    return $routes;
  }

  public static function execute() {
    try {
      $routes = self::getRoutes();
      $method = Request::get();
      $path = Uri::get('path');

      if(!isset($routes[$method])) {
        throw new \Exception('Route Method not found in its API', 404);
      }

      // Itera sobre as rotas definidas para o método atual
      foreach ($routes[$method] as $route => $handler) {
        // Tenta fazer a correspondência da rota com o caminho solicitado
        if (preg_match('#^' . $route . '$#', $path, $matches)) {
          // Remove a primeira entrada que corresponde à URL completa
          array_shift($matches);
          // Chama o manipulador da rota com os parâmetros capturados
          $handler(...$matches);
          return;
        }
      }

      throw new \Exception('Route not found', 404);

    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }
}
