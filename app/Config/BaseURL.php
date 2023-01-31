<?php


namespace App\PHPLoginManagement\Config;

require_once __DIR__ . '/conf.php';


class BaseURL
{
  private static ?string $baseurl = null;
  public static function get()
  {
    if (is_null(self::$baseurl)) {
      self::$baseurl = getConfig()['baseurl'];
    }
    return self::$baseurl;
  }
}
