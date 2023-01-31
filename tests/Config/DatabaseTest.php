<?php

namespace App\PHPLoginManagement\Config;

use PDO;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
  public function testGetConnection()
  {
    $connection = Database::getConnection();
    $this->assertEquals($connection::class, PDO::class);
  }
}
