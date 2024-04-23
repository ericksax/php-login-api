<?php
namespace Erick\PhpLoginApi\app\services;
use Erick\PhpLoginApi\database\Connection;
use Firebase\JWT\JWT;

class LoginService {
  private $pdo;
  private $data;

  public function __construct() {
    $this->pdo = Connection::connect();
    $this->data = json_decode(file_get_contents('php://input'), true);
  }

  public function login() {
    $userLogin = filter_var($this->data['login'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userPass = filter_var($this->data['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if(!$userLogin || !$userPass) {
      http_response_code(400);
      throw new \Exception('missing credentials', 400);
    }

    $query = "SELECT * FROM users WHERE login = :login";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':login', $userLogin);
    $stmt->execute();
    $user = $stmt->fetch();

    if(!$user) {
     http_response_code(404);
     throw new \Exception('User not found', 404);
    }

    if(!password_verify($userPass, $user->password)) {
      http_response_code(401);
      throw new \Exception('Invalide credentials');
    }

    $payload = [
      'user_id' => $user->id,
      "is_admin" => $user->is_admin,
      'exp' => time() + (60 * 60), 
    ];

    $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

    // Retornar o token para o usuário
    echo json_encode([
      'token' => $token,
      'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'login' => $user->login
      ]
    ]);
  }
}
