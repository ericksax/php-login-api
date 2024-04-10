<?php

namespace Erick\PhpLoginApi\database;
use Dotenv;
use PDO;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, 'myconfig');
$dotenv->load();

class Connection {

    public static function connect() {
        return new PDO("pgsql:host=db;dbname=" . $_ENV['POSTGRES_DB'], $_ENV['POSTGRES_USER'], $_ENV['POSTGRES_PASSWORD'], [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
    }
}
