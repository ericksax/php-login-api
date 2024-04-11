<?php

namespace Erick\PhpLoginApi\app\services;

use DateTime;
use Erick\PhpLoginApi\database\Connection;
use PDO;

class User
{
  private $data;
  private $pdo;

  function __construct()
  {
    $this->data = json_decode(file_get_contents("php://input"), true);
    $this->pdo = Connection::connect();
  }

  function create()
  {
    $login = filter_var($this->data['login'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($this->data['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_var($this->data['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $phone = filter_var($this->data['phone'], FILTER_SANITIZE_NUMBER_INT);
    $is_admin = filter_var($this->data['is_admin'], FILTER_VALIDATE_BOOLEAN);

    if (!$login || !$name || !$password || !$phone) {
      http_response_code(400); // Requisição inválida
      echo json_encode(['message' => 'Todos os campos são obrigatórios']);
      exit();
    }

    if (strlen($password) < 8) {
      http_response_code(400); // Requisição inválida
      echo json_encode(['message' => 'A senha deve ter pelo menos 8 caracteres']);
      exit();
    }

    if ($this->UserExists($login)) {
      http_response_code(409); // Conflito
      echo json_encode(['message' => 'Usuário já existe']);
      exit();
    }

    try {
      $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);

      $user = [
        'login' => $login,
        'name' => $name,
        'phone' => $phone,
        'password' => $encryptedPassword,
      ];


      if ($is_admin) {
        $user['is_admin'] = true;
      }

      // Montar a query SQL com base no array $user
      $query = 'INSERT INTO users (login, name, phone, ';

      // Adicionar is_admin à query se estiver definido no array $user
      if (isset($user['is_admin'])) {
        $query .= 'is_admin, ';
      }

      $query .= 'password) VALUES (:login, :name, :phone, ';

      // Adicionar marcador de posição para is_admin se estiver definido no array $user
      if (isset($user['is_admin'])) {
        $query .= ':is_admin, ';
      }

      $query .= ':password)';

      $statement = $this->pdo->prepare($query);
      $statement->execute($user);

      http_response_code(201);
      echo json_encode(['message' => 'Usuário criado com sucesso']);
      exit();
    } catch (\PDOException $e) {
      http_response_code(500); // Erro interno do servidor
      echo json_encode(['message' => 'Erro no servidor: ' . $e->getMessage()]);
      exit();
    }
  }


  private function UserExists($login)
  {
    $query = 'SELECT * FROM users WHERE login = :login';
    $statement = $this->pdo->prepare($query);
    $statement->execute(['login' => $login]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user ? true : false;
  }

  function read()
  {
    $query = 'SELECT id, name, phone, is_admin, login, created_at, updated_at FROM users';
    $statement = $this->pdo->prepare($query);
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
  }

  function retrieve($id)
  {
    try {
      $query = 'SELECT id, name, login, phone, is_admin, created_at, updated_at FROM users WHERE id = :id';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['id' => $id]);
      $user = $statement->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        throw new \Exception('User does not exists');
      }

      echo json_encode($user);
    } catch (\PDOException $e) {
      echo json_encode(['message' => 'Erro ao buscar usuário: ' . $e->getMessage()]);
    } catch (\Exception $e) {
      echo json_encode(['message' => $e->getMessage()]);
    }
  }

  function update($id) {
  try {
    $query = 'SELECT id, name, login, phone, is_admin, created_at, updated_at FROM users WHERE id = :id';
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      throw new \Exception('User does not exists');
    }

    $name = filter_var($this->data['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $phone = filter_var($this->data['phone'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $login = filter_var($this->data['login'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = 'UPDATE users SET name = :name, phone = :phone, login = :login, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
    $statement = $this->pdo->prepare($query);
    $newUser = [
      'id' => $id,
      'name' => $name,
      'phone' => $phone,
      'login' => $login,
    ];
    $statement->execute($newUser);

    http_response_code(200);
    echo json_encode(['message' => 'Usário atualizado com sucesso', 'user' => $newUser]);
    exit();


  } catch (\PDOException $e) {
    echo json_encode(['message' => 'Erro ao buscar usuário: ' . $e->getMessage()]);
  } catch (\Exception $e) {
    echo json_encode(['message' => $e->getMessage()]);
  }
  }


  function delete($id) {
    try {
      $query = 'SELECT id, name, login, phone, is_admin, created_at, updated_at FROM users WHERE id = :id';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['id' => $id]);
      $user = $statement->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        throw new \Exception('User does not exists');
      }

      $query = 'DELETE FROM users WHERE id = :id';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['id' => $id]);

      http_response_code(200);
      echo json_encode(['message' => 'Usário excluído com sucesso']);
      exit();


    } catch (\PDOException $e) {
      echo json_encode(['message' => 'Erro ao buscar usuário: ' . $e->getMessage()]);
    } catch (\Exception $e) {
      echo json_encode(['message' => $e->getMessage()]);
    }

  }
}
