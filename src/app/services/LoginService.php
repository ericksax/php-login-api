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
      throw new \Exception('missing credentials', 401);
    }

    $query = "SELECT * FROM users WHERE login = :login";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':login', $userLogin);
    $stmt->execute();
    $user = $stmt->fetch();

    if(!$user) {
     throw new \Exception('User not found', 404);
    }

    if(!password_verify($userPass, $user->password)) {
      throw new \Exception('Invalide credentials', 401);
    }

    $payload = [
      'user_id' => $user->id,
      'exp' => time() + (60 * 60), // Token expira em 1 hora
    ];

    $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

    // Retornar o token para o usuário
    echo json_encode([
      'token' => $token
    ]);
  }
}
