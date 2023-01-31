<?php

namespace App\PHPLoginManagement\Controller;

use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
  public function testIndex()
  {
    $controller = new HomeController;
    $controller->index();
    $this->expectOutputRegex("[html]");
    $this->expectOutputRegex("[PHP Login Management]");
    $this->expectOutputRegex("[Register]");
    $this->expectOutputRegex("[Login]");
  }
}
