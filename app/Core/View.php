<?php

namespace App\PHPLoginManagement\Core;

class View
{
  public static function render(string $path, array $model)
  {
    require __DIR__ . "/../View/header.php";
    require __DIR__ . "/../View/" . $path . '.php';
    require __DIR__ . "/../View/footer.php";
  }
}
