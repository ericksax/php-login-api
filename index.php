<?php

require './vendor/autoload.php';

use Erick\PhpLoginApi\app\routes\Routes;

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

Routes::execute();

