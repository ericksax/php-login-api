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
      $query = 'SELECT * FROM documents WHERE id = :id';
      $statement = $this->pdo->prepare($query);
      $statement->execute(['id' => $id]);
      $document = $statement->fetch(\PDO::FETCH_ASSOC);

      if(!$document) {
        http_response_code(404);
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
        http_response_code(404);
        throw new \Exception("Document not found", 404);
      }

      if(!$this->data) {
        echo json_encode($this->data);
        http_response_code(400);
        throw new \Exception("Dados inválidos", 400);
      }
      $imagePath =  __DIR__ . '\\..\\..\\..\\uploads\\' . $this->image['name'];

      $query = 'UPDATE documents SET recebedor_nome = :recebedor_nome, recebedor_documento = :recebedor_documento, atualizado_em = :atualizado_em, image_url = :image_url WHERE id = :id';
      $statement = $this->pdo->prepare($query);
      $statement->execute([
        'id' => $id,
        'recebedor_nome' => $this->data['recebedor_nome'],
        'recebedor_documento' =>$this->data['recebedor_documento'],
        'image_url' => str_replace('\\', '\\\\', 'http:\\') . 'localhost:8000\\uploads\\' . $this->image['name'],
        'atualizado_em' => date('Y-m-d H:i:s')
      ]);

      move_uploaded_file($this->image['tmp_name'], $imagePath);

      http_response_code(200);
      echo json_encode(['message' => 'Document updated successfully']);
    } catch(\Throwable $th) {
      http_response_code((int) $th->getCode());
      echo $th->getMessage();
    }
  }
}