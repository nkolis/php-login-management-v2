<?php

namespace App\PHPLoginManagement\Config;

use PDO;
use PDOException;

class Database
{
  private static ?PDO $connection = null;

  public static function getConnection($mode = 'test'): PDO
  {
    try {
      if (is_null(self::$connection)) {
        $config = DATABASE_CONFIG;
        $host = $config['database'][$mode]['host'];
        $dbname = $config['database'][$mode]['dbname'];
        $dsn = "mysql:host=$host;dbname=$dbname";
        $username = $config['database'][$mode]['username'];
        $password = $config['database'][$mode]['password'];
        $options = [
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        self::$connection = new PDO($dsn, $username, $password, $options);
      }
      return self::$connection;
    } catch (PDOException $e) {
      throw $e;
    }
  }

  public static function beginTransaction(): void
  {
    self::$connection->beginTransaction();
  }

  public static function commitTransaction(): void
  {
    self::$connection->commit();
  }

  public static function rollbackTransaction(): void
  {
    self::$connection->rollBack();
  }
}
