<?php

namespace App\PHPLoginManagement\Core;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
  public function testAddRouter()
  {
    Router::add(method: 'GET', path: '/', controller: HomeController::class, function: 'index', middleware: []);
    $this->expectOutputRegex("[HELLO, WORLD]");
  }
}
