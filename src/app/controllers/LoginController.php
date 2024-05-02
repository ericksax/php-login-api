<?php

namespace Erick\PhpLoginApi\app\controllers;

use Erick\PhpLoginApi\app\services\LoginService;

class LoginController
{

  public function login()
  {
    $login = new LoginService;
    $login->Login();
  }
}
