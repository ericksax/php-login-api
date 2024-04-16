<?php
namespace Erick\PhpLoginApi\app\services;
use Erick\PhpLoginApi\database\Connection;

class DocumentService
{
  private $pdo;
  private $data;

  public function __construct()
  {
    $this->data = json_decode(file_get_contents("php://input"), true);
    $this->pdo = Connection::connect();
  }

  public function read($id)
  {
    try {
      $query = 'SELECT * FROM documents WHERE id = :id';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['id' => $id]);
      $document = $statement->fetch(\PDO::FETCH_ASSOC);

      if(!$document) {
        throw new \Exception('Document not found', 404);
      }

      http_response_code(200);
      echo json_encode($document, JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
      http_response_code($e->getCode());
      echo json_encode(['message' => $e->getMessage()]);
    }
  }

  public function update($id)
  {
    try {
      $query = 'SELECT * FROM documents WHERE id = :id';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['id' => $id]);
      $document = $statement->fetch(\PDO::FETCH_ASSOC);

      if(!$document) {
        throw new \Exception('Document not found', 404);
      }

      if(!isset($this->data['recebedor_nome']) || !isset($this->data['recebedor_documento']) || !isset($this->data['image_url'])) {
        throw new \Exception('All fields are required', 400);
      }

      $tmpName = $this->data['image_url'];
      $image = $this->data['image_path'];
      $path = '../assets/images/' . uniqid().'_'. $tmpName;  

      move_uploaded_file($image, $path);

      $query = 'UPDATE documents SET recebedor_nome = :recebedor_nome, recebedor_documento = :recebedor_documento, image_url = :image_url, atualizado_em = :atualizado_em WHERE id = :id';
      $statement = $this->pdo->prepare($query);
      $statement->execute([
        'id' => $id,
        'recebedor_nome' => $this->data['recebedor_nome'],
        'recebedor_documento' => $this->data['recebedor_documento'],
        'image_url' => $tmpName,
        'atualizado_em' => date('Y-m-d H:i:s'),
      ]);
      http_response_code(200);
      echo json_encode(['message' => 'Document updated successfully'], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode(['message' => $e->getMessage()]);
    }
  }
}