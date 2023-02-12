<?php


namespace App\PHPLoginManagement\Config;


class PHPMailerServer
{
  private static ?array $config = null;
  public static function get(): ?array
  {
    if (is_null(self::$config)) {
      $connection = Database::getConnection();
      $statement = $connection->query("SELECT host, username, password FROM php_mailer_server");
      self::$config = $statement->fetch();
    }
    return self::$config;
  }
}
