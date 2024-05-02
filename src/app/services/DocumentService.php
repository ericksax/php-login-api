<?php

namespace Erick\PhpLoginApi\app\services;

use Erick\PhpLoginApi\database\Connection;

class DocumentService
{
  private $pdo;
  private $data;
  private $image;

  public function __construct()
  {
    $this->data = $_POST;
    $this->image = $_FILES['file'];
    $this->pdo = Connection::connect();
  }

  public function read($id)
  {
    try {
      $query = 'SELECT dc.*, 
      u.nome_completo, 
      u.telefone, 
      u.usuario, 
      u.admin, 
      u.data_cadastro, 
      u.data_atualizacao, 
      u.idusuario_transportadora
      FROM documento_canhoto dc
      JOIN usuario_transportadora u ON dc.idusuario_transportadora = u.idusuario_transportadora
      WHERE dc.iddocumento = :iddocumento;
      ';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['iddocumento' => $id]);
      $document = $statement->fetch(\PDO::FETCH_ASSOC);

      if (!$document) {
        http_response_code(404);
        throw new \Exception('Document not found', 404);
      }

      http_response_code(200);
      echo json_encode($document, JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode(['message' => $e->getMessage()]);
    }
  }

  public function readByNFKey()
  {
    $chave =  filter_var($_GET['chave'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
      $query = 'SELECT dc.*, 
      u.nome_completo, 
      u.telefone, 
      u.usuario, 
      u.admin, 
      u.data_cadastro, 
      u.data_atualizacao, 
      u.idusuario_transportadora
      FROM documento_canhoto dc
      JOIN usuario_transportadora u ON dc.idusuario_transportadora = u.idusuario_transportadora
      WHERE dc.chave_acesso = :chave_acesso;
      ';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['chave_acesso' => $chave]);
      $document = $statement->fetch(\PDO::FETCH_ASSOC);

      if (!$document) {
        http_response_code(404);
        throw new \Exception('Document not found', 404);
      }

      http_response_code(200);
      echo json_encode($document, JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode(['message' => $e->getMessage()]);
    }
  }

  public function update($id)
  {
    try {
      $query = 'SELECT * FROM documento_canhoto WHERE iddocumento = :iddocumento';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['iddocumento' => $id]);
      $document = $statement->fetch(\PDO::FETCH_ASSOC);

      if (!$document) {
        http_response_code(404);
        throw new \Exception("Document not found", 404);
      }

      if (!$this->data) {
        echo json_encode($this->data);
        http_response_code(400);
        throw new \Exception("Dados inválidos", 400);
      }
      $imagePath =  __DIR__ . '\\..\\..\\..\\uploads\\' . $this->image['name'];

      $query = 'UPDATE documento_canhoto SET';
      $params = [];

      if (isset($this->data['recebedor_nome'])) {
        $query .= ' recebedor_nome = :recebedor_nome,';
        $params['recebedor_nome'] = $this->data['recebedor_nome'];
      }

      if (isset($this->data['recebedor_documento'])) {
        $query .= ' recebedor_documento = :recebedor_documento,';
        $params['recebedor_documento'] = $this->data['recebedor_documento'];
      }

      if (isset($this->image['name'])) {
        $query .= ' foto_canhoto = :foto_canhoto,';
        $params['foto_canhoto'] = str_replace('\\', '\\\\', 'http:\\') . 'localhost:8000\\uploads\\' . $this->image['name'];
      }

      $query .= ' data_atualizacao = :data_atualizacao WHERE iddocumento = :iddocumento';
      $params['iddocumento'] = $id;
      $params['data_atualizacao'] = date('Y-m-d H:i:s');

      // Remover a última vírgula, se houver
      $query = rtrim($query, ',');

      $statement = $this->pdo->prepare($query);
      $statement->execute($params);


      move_uploaded_file($this->image['tmp_name'], $imagePath);

      http_response_code(200);
      echo json_encode(['message' => 'Document updated successfully']);
    } catch (\Throwable $th) {
      http_response_code((int) $th->getCode());
      echo $th->getMessage();
    }
  }
}
