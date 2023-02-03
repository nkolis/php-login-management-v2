<?php

namespace App\PHPLoginManagement\Core;

use App\PHPLoginManagement\Config\BaseURL;

class View
{
  public static function render(string $path, array $model)
  {
    require __DIR__ . "/../View/header.php";
    require __DIR__ . "/../View/" . $path . '.php';
    require __DIR__ . "/../View/footer.php";
  }

  public static function redirect($url)
  {
    header("Location: " . BaseURL::get() . $url);
    if (getenv('mode') != 'test') {
      exit();
    }
  }
}
