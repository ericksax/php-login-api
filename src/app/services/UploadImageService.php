<?php

use Erick\PhpLoginApi\database\Connection;

class UploadImageService
{
    private $pdo;
    private $image;

    public function __construct()
    {
        $this->image = $_FILES['image'];
        $this->pdo = Connection::connect();
    }

    public function uploadImage($userId, $documentId) {
        $tmpName = $this->image['tmp_name'];
        $name = $this->image['name'];
        $path = '../assets/images/' . uniqid().'_'.$name;  

        move_uploaded_file($tmpName, $path);

        $query = "INSERT INTO images (path, name, document_id, user_id) VALUES (:path)";
        $statement = $this->pdo->prepare($query);
        $statement->execute([
            'path' => $tmpName,
            'name' => $name,
            'document_id' => $documentId,
            'user_id' => $userId
        ]);
    }
    
    public function getImage($documentId) {
        $query = "SELECT path FROM images WHERE document_id = :document_id";
        $statement = $this->pdo->prepare($query);
        $statement->execute(['document_id' => $documentId]);
        $imagePath = $statement->fetch(PDO::FETCH_ASSOC);
    
        if ($imagePath) {
            http_response_code(200);
            echo json_encode($imagePath);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Imagem não encontrada para o documento especificado']);
        }
    }
}
