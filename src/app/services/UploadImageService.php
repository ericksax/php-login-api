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

    public function uploadImage($documentId) {
        $tmpName = $this->image['tmp_name'];
        $image = $this->image['image_path'];
        $name = $this->image['name'];
        $path = '../assets/images/' . uniqid().'_'.$name;  

        echo $image;

        if(!move_uploaded_file($image, $path)){
            
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao fazer upload da imagem']);
            exit;
        };

        $query = "INSERT INTO images (path, name, document_id VALUES (:path)";
        $statement = $this->pdo->prepare($query);
        $statement->execute([
            'path' => $tmpName,
            'name' => $name,
            'document_id' => $documentId
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
