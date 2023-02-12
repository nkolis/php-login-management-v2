<?php

namespace App\PHPLoginManagement\Config;

use PDO;
use PHPUnit\Framework\TestCase;

class PHPMailerServerTest extends TestCase
{
  public function testGetPHPMailerServerConfig()
  {
    $result = PHPMailerServer::get();
    $this->assertIsArray($result);
    $this->assertTrue(sizeof($result) > 1);
  }
}
