<?php
namespace Erick\PhpLoginApi\app\controllers;

use Erick\PhpLoginApi\app\services\User;

class UserController {

  public function createUser() {
    $user = new User;
    $user->create();
  }

  public function showUsers() {
    $user = new User;
    $user->read();
  }

  public function retrieve($id) {
    $user = new User;
    $user->retrieve($id);
  }

  public function updateUser($id) {
    $user = new User;
    $user->update($id);
  }

  public function deleteUser($id) {
    $user = new User;
    $user->delete($id);
  }
}
