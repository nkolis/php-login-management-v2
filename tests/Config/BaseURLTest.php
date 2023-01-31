<?php

namespace App\PHPLoginManagement\Config;

use PHPUnit\Framework\TestCase;

class BaseURLTest extends TestCase
{
  public function testGetBaseURL()
  {
    self::assertEquals(BaseURL::get(), 'http://localhost/php-login-management-v2/public');
  }
}
