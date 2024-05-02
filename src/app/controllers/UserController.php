<?php

namespace Erick\PhpLoginApi\app\controllers;

use Erick\PhpLoginApi\app\services\UserService;

class UserController
{

  public function createUser()
  {
    $user = new UserService;
    $user->create();
  }

  public function showUsers()
  {
    $user = new UserService;
    $user->read();
  }

  public function retrieve($id)
  {
    $user = new UserService;
    $user->retrieve($id);
  }

  public function updateUser($id)
  {
    $user = new UserService;
    $user->update($id);
  }

  public function deleteUser($id)
  {
    $user = new UserService;
    $user->delete($id);
  }
}
