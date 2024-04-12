<?php

namespace Erick\PhpLoginApi\app\controllers;

use UploadImageService;

class ImageController 
{
    public function uploadImage($userId, $documentId) {
        $image = new UploadImageService();
        $image->uploadImage($userId, $documentId);
    }
}