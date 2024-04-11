<?php
require './vendor/autoload.php';
use Dotenv\Dotenv;
use Erick\PhpLoginApi\app\routes\Routes;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

Routes::execute();

