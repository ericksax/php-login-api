<?php
namespace Erick\PhpLoginApi\app\controllers;

use Erick\PhpLoginApi\app\helpers\Uri;
use User;

class UserController {
  // public function index() {
  //   $user = new User;
  //   return $user->create();
  // }

  public function show() {
    var_dump(Uri::get('path'));
    echo 'nois';
  }
}
