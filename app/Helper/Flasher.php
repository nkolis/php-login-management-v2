<?php

namespace App\PHPLoginManagement\Helper;


class Flasher
{


  public static function set(array $message): void
  {
    if (!isset($_SESSION['flasher'])) {
      $_SESSION['flasher'] = $message;
    }
  }

  public static function get(): ?array
  {
    if (isset($_SESSION['flasher'])) {
      $flasher = $_SESSION['flasher'];
      self::remove();
      return $flasher;
    }
    return null;
  }

  public static function remove(): void
  {
    unset($_SESSION['flasher']);
  }
}
