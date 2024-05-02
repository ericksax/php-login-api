<?php

namespace Erick\PhpLoginApi\database;

use PDO;

class Connection
{

    public static function connect()
    {
        return new PDO("pgsql:host=" . $_ENV['HOST'] . ";port=" . $_ENV['PORT'] . ";dbname=" . $_ENV['POSTGRES_DB'], $_ENV['POSTGRES_USER'], $_ENV['POSTGRES_PASSWORD'], [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
    }
}
