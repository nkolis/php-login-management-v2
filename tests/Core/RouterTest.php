<?php

namespace App\PHPLoginManagement\Core;

use App\PHPLoginManagement\Controller\HomeController;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
  public function testRouter()
  {
    $_SERVER['PATH_INFO'] = '/';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    Router::add(method: 'GET', path: '/', controller: HomeController::class, function: 'index', middleware: []);
    Router::run();
    $this->expectOutputRegex("[html]");
    $this->expectOutputRegex("[PHP Login Management]");
  }

  public function testRouterNotfound()
  {
    $_SERVER['PATH_INFO'] = '/nofoud';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    Router::add(method: 'GET', path: '/', controller: HomeController::class, function: 'index', middleware: []);
    Router::run();
    $this->expectOutputRegex("[CONTROLLER NOT FOUND]");
  }
}
