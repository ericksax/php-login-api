<?php

namespace src\app\helpers;

class Uri
{
  public static function get(string $param)
  {
    return parse_url($_SERVER['REQUEST_URI'])[$param];
  }
}
