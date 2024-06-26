<?php

namespace src\app\services;

use src\database\Connection;
use PDO;

class UserService
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
    $usuario = filter_var($this->data['usuario'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $nome_completo = filter_var($this->data['nome_completo'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $senha = filter_var($this->data['senha'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cpfcnpj = filter_var($this->data['cpfcnpj'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $telefone = filter_var($this->data['telefone'], FILTER_SANITIZE_NUMBER_INT);

    if (!isset($this->data['admin'])) {
      $admin = 0;
    } else {
      $admin = filter_var($this->data['admin'], FILTER_SANITIZE_NUMBER_INT);
    }

    if (!$usuario || !$nome_completo || !$senha || !$telefone || !$cpfcnpj) {
      http_response_code(400); // Requisição inválida
      echo json_encode(['message' => 'Todos os campos são obrigatórios']);
      exit();
    }

    if (strlen($senha) < 8) {
      http_response_code(400); // Requisição inválida
      echo json_encode(['message' => 'A senha deve ter pelo menos 8 caracteres']);
      exit();
    }

    if ($this->UserExists($usuario)) {
      http_response_code(409); // Conflito
      echo json_encode(['message' => 'Usuário já existe']);
      exit();
    }

    try {
      $encryptedPassword = password_hash($senha, PASSWORD_DEFAULT);

      $user = [
        'usuario' => $usuario,
        'nome_completo' => $nome_completo,
        'telefone' => $telefone,
        'cpfcnpj' => $cpfcnpj,
        'senha' => $encryptedPassword,
      ];


      if ($admin) {
        $user['admin'] = 1;
      }

      // Montar a query SQL com base no array $user
      $query = 'INSERT INTO usuario_transportadora (usuario, nome_completo, telefone, cpfcnpj, ';

      // Adicionar admin à query se estiver definido no array $user
      if (isset($user['admin'])) {
        $query .= 'admin, ';
      }

      $query .= 'senha) VALUES (:usuario, :nome_completo, :telefone, :cpfcnpj,';

      // Adicionar marcador de posição para admin se estiver definido no array $user
      if (isset($user['admin'])) {
        $query .= ':admin, ';
      }

      $query .= ':senha)';

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

  private function UserExists($usuario)
  {
    $query = 'SELECT * FROM usuario_transportadora WHERE usuario = :usuario';
    $statement = $this->pdo->prepare($query);
    $statement->execute(['usuario' => $usuario]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user ? true : false;
  }

  function read()
  {
    try {
      $query = 'SELECT idusuario_transportadora, nome_completo, telefone, admin, usuario, cpfcnpj, data_cadastro, data_atualizacao FROM usuario_transportadora';
      $statement = $this->pdo->prepare($query);
      $statement->execute();
      $users = $statement->fetchAll(PDO::FETCH_ASSOC);
  
      http_response_code(200);
      echo json_encode($users);

    } catch (\PDOException $e) {
      http_response_code(500);
      throw new \Exception('Erro ao buscar usuários: ' . $e->getMessage());
    }
  }

  function retrieve($id)
  {
    try {
      $query = 'SELECT idusuario_transportadora, nome_completo, usuario, telefone, admin, cpfcnpj, data_cadastro, data_atualizacao FROM usuario_transportadora WHERE idusuario_transportadora = :idusuario_transportadora';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['idusuario_transportadora' => $id]);
      $user = $statement->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        throw new \Exception('User does not exists');
      }

      echo json_encode($user);
    } catch (\PDOException $e) {
      echo json_encode(['message' => 'Erro ao buscar usuário: ' . $e->getMessage()]);
    } 
  }

  function update($id)
  {
    try {
      $query = 'SELECT idusuario_transportadora, nome_completo, usuario, telefone, admin, cpfcnpj, data_cadastro, data_atualizacao FROM usuario_transportadora WHERE idusuario_transportadora = :idusuario_transportadora';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['idusuario_transportadora' => $id]);
      $user = $statement->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        throw new \Exception('User does not exists', 404);
      }

      $nome_completo = filter_var($this->data['nome_completo'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $telefone = filter_var($this->data['telefone'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $usuario = filter_var($this->data['usuario'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $cpfcnpj= filter_var($this->data['cpfcnpj'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

      $query = 'UPDATE usuario_transportadora SET';
      $updates = [];
      $params = [];

      // Verifica e adiciona cada campo para atualização apenas se não estiver vazio
      if (!empty($nome_completo)) {
        $updates[] = ' nome_completo = :nome_completo';
        $params['nome_completo'] = $nome_completo;
      }

      if (!empty($telefone)) {
        $updates[] = ' telefone = :telefone';
        $params['telefone'] = $telefone;
      }

      if (!empty($usuario)) {
        $updates[] = ' usuario = :usuario';
        $params['usuario'] = $usuario;
      }

      if (!empty($cpfcnpj)) {
        $updates[] = ' cpfcnpj = :cpfcnpj';
        $params['cpfcnpj'] = $cpfcnpj;
      }

      // Adiciona a cláusula SET apenas se houver campos para atualização
      if (!empty($updates)) {
        $query .= implode(',', $updates);
        $query .= ' , data_atualizacao = CURRENT_TIMESTAMP';
        $query .= ' WHERE idusuario_transportadora = :idusuario_transportadora';
        $params['idusuario_transportadora'] = $id;

        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
      } else {
        http_response_code(400);
        echo json_encode(["message" => "Nenhum campo fornecido para atualização."]);
      }

      http_response_code(200);
      echo json_encode(['message' => 'Usário atualizado com sucesso']);
      exit();
    } catch (\Exception $e) {
      echo json_encode(['message' => $e->getMessage()]);
    }
  }

  function delete($id)
  {
    try {
      $query = 'SELECT idusuario_transportadora, nome_completo, usuario, telefone, admin, cpfcnpj, data_cadastro, data_atualizacao FROM usuario_transportadora WHERE idusuario_transportadora = :idusuario_transportadora';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['idusuario_transportadora' => $id]);
      $user = $statement->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        throw new \Exception('User does not exists');
      }

      $query = 'DELETE FROM usuario_transportadora WHERE idusuario_transportadora = :idusuario_transportadora';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['idusuario_transportadora' => $id]);

      http_response_code(200);
      echo json_encode(['message' => 'Usário excluído com sucesso']);
      exit();
    } catch (\PDOException $e) {
      echo json_encode(['message' => 'Erro ao buscar usuário: ' . $e->getMessage()]);
    } 
  }
}
