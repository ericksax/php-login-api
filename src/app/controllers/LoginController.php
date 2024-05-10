<?php

namespace src\app\controllers;

use src\app\services\LoginService;

class LoginController
{

  public function login()
  {
    $login = new LoginService;
    $login->Login();
  }
}
