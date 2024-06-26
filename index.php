<?php
require './vendor/autoload.php';
use Dotenv\Dotenv;
use src\app\routes\Routes;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Content-Disposition");

Routes::execute();

