<?php

use Erick\PhpLoginApi\database\Connection;

class User {
  private $data;
  private $pdo;

  function __construct() {
    $this->data = json_decode(file_get_contents("php://input"), true);
    $this->pdo = Connection::connect();
  }

  function create() {
    $login  = filter_var($this->data['login'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($this->data['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_var($this->data['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$login || !$name || !$password) {
      http_response_code(400); // Requisição inválida
      echo json_encode(['message' => 'Todos os campos são obrigatórios']);
      exit();
    }

    if ($this->UserExists($login)) {
      http_response_code(409); // Conflict
      echo json_encode(['message' => 'Usuário ja existe']);
      exit();
    }

    try {
      $query = 'INSEWRT INTO users (login, name, password) VALUES (:login, :name, :password)';
      $statement = $this->pdo->prepare($query);
      $statement->execute([
        'login' => $login,
        'name' => $name,
        'password' => $password
      ]);

      http_response_code(201);
      echo json_encode(['message' => 'Usuário criado com sucesso']);
      exit();

    } catch (PDOException $e) {
      http_response_code(500); // Erro interno do servidor
      echo json_encode(['message' => 'Erro no servidor ' . $e->getMessage()]);
      exit();
    }
  }

  private function UserExists($cnpj) {
    $query = 'SELECT * FROM users WHERE cnpj = :login';
    $statement = $this->pdo->prepare($query);
    $statement->execute(['login' => $cnpj]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user ? true : false;
  }
}

// require '../../vendor/autoload.php';

// use Erick\PhpLoginApi\database\Connection;

// header('Access-Control-Allow-Origin: *');
// header('Content-Type: application/json');

// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     http_response_code(405); // Método não permitido
//     echo json_encode(['message' => 'Método não permitido']);
//     exit();
// }

// $pdo = Connection::connect();

// // Obtém o conteúdo do corpo da requisição
// $data = json_decode(file_get_contents("php://input"), true);

// // Verifica se os campos necessários estão presentes no JSON
// if (!isset($data['cnpj'], $data['name'], $data['password'])) {
//     http_response_code(400); // Requisição inválida
//     echo json_encode(['message' => 'Todos os campos são obrigatórios']);
//     exit();
// }

// // Filtra e obtém os dados do JSON
// $cnpj = filter_var($data['cnpj'], FILTER_SANITIZE_NUMBER_INT);
// $name = filter_var($data['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
// $password = filter_var($data['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// if (!$cnpj || !$name || !$password) {
//     http_response_code(400); // Requisição inválida
//     echo json_encode(['message' => 'Todos os campos são obrigatórios']);
//     exit();
// }


// // Verifica se o motorista existe
// $query = 'SELECT * FROM drivers WHERE cnpj = :cnpj';

// try {
//     $statement = $pdo->prepare($query);
//     $statement->execute(['cnpj' => $cnpj]);
//     $user = $statement->fetch(PDO::FETCH_ASSOC);

//     if ($user) {
//         http_response_code(409); // Conflict
//         echo json_encode(['message' => 'Usuário já existe']);
//         exit();
//     }

// } catch( PDOException $e) {
//     http_response_code(500); // Erro interno do servidor
//     echo json_encode(['message' => 'Erro ao verificar motorista: ' . $e->getMessage()]);
//     exit();
// }


// try {
//     $query = 'INSERT INTO drivers (cnpj, name, password) VALUES (:cnpj, :name, :password)';
//     $statement = $pdo->prepare($query);
//     $statement->execute([
//         'cnpj' => $cnpj,
//         'name' => $name,
//         'password' => $password
//     ]);

//     $user = $statement->fetch(PDO::FETCH_ASSOC);

//     http_response_code(201); // Criado
//     echo json_encode(['message' => 'Motorista criado com sucesso', 'user' => $user]);
// } catch (PDOException $e) {
//     http_response_code(500); // Erro interno do servidor
//     echo json_encode(['message' => 'Erro ao criar motorista: ' . $e->getMessage()]);
// }
