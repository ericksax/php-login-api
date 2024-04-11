<?php
namespace Erick\PhpLoginApi\app\controllers;

use Erick\PhpLoginApi\app\services\Login;

class LoginController {

  public function login() {
    $login = new Login;
    $login->Login();
  }
}
