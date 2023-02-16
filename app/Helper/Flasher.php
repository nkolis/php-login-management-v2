<?php

namespace App\PHPLoginManagement\Helper;


class Flasher
{

  private static function session_start()
  {
    if (empty(session_id()) && !headers_sent()) {
      session_start();
    }
  }

  public static function set(array $message): void
  {
    self::session_start();
    if (!isset($_SESSION['flasher'])) {
      $_SESSION['flasher'] = $message;
    }
  }

  public static function get(): ?array
  {
    self::session_start();

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
