<?php

namespace App\PHPLoginManagement\Config;

require __DIR__ . '/conf.php';

class BaseURL
{
  public static function get()
  {
    $config = getConfig();
    return $config['baseurl'];
  }
}
