<?php

namespace src\app\controllers;

use src\app\services\DocumentService;

class DocumentController
{
    public function read($id)
    {
        $service = new DocumentService();
        $service->read($id);
    }

    public function updateDocument($id)
    {
        $service = new DocumentService();
        $service->update($id);
    }

    public function readByNFKey()
    {

        $service = new DocumentService();
        $service->readByNFKey();
    }
}
