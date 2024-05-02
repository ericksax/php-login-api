<?php

namespace Erick\PhpLoginApi\app\helpers;

class Request
{
  public static function get()
  {
    return $_SERVER['REQUEST_METHOD'];
  }
}
