<?php

namespace src\app\helpers;

class Request
{
  public static function get()
  {
    return $_SERVER['REQUEST_METHOD'];
  }
}
