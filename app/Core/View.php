<?php

namespace App\PHPLoginManagement\Core;

use App\PHPLoginManagement\Config\BaseURL;

class View
{
  public static function render(?string $path, array $model)
  {
    if ($path != null) {
      require __DIR__ . "/../View/header.php";
      require __DIR__ . "/../View/" . $path . '.php';
      require __DIR__ . "/../View/footer.php";
    }
  }

  public static function redirect($url)
  {
    header("Location: " . BASE_URL . $url);
    if (getenv('mode') != 'test') {
      exit();
    }
  }

  public static function redirectSwal(string $url, string $path,  array $swal, array $model = [])
  {
    self::render($path, $model);
    $base = BASE_URL;
    echo "<script> Swal.fire({
      title: '{$swal['message']}',
      icon: '{$swal['icon']}',
      showConfirmButton: false,
      timer: 1500,
    }).then(function() {
      location='$base$url'
    });</script>";
    //header("Location: " . BASE_URL . $url);
    if (getenv('mode') != 'test') {
      exit();
    }
  }
}
