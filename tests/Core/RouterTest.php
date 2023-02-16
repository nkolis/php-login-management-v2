<?php

namespace App\PHPLoginManagement\Core;

require_once __DIR__ . '/../Helper/helper.php';


use App\PHPLoginManagement\Controller\HomeController;
use App\PHPLoginManagement\Controller\UserController;
use App\PHPLoginManagement\Middleware\MustLoginMiddleware;
use App\PHPLoginManagement\Middleware\MustNotLoginMiddleware;
use App\PHPLoginManagement\Service\SessionService;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

  function setUp(): void
  {
    putenv("mode=test");
    $_COOKIE[SessionService::$COOKIE] = '';
  }

  public function testRouterHomeGuest()
  {
    $_SERVER['PATH_INFO'] = '/';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    Router::add(method: 'GET', path: '/', controller: HomeController::class, function: 'index', middleware: [MustNotLoginMiddleware::class]);
    Router::run();
    $this->expectOutputRegex("[html]");
    $this->expectOutputRegex("[PHP Login Management]");
  }

  public function testRouterNotFound()
  {
    $_SERVER['PATH_INFO'] = '/nofoud';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    Router::add(method: 'GET', path: '/', controller: HomeController::class, function: 'index', middleware: []);
    Router::run();
    $this->expectOutputRegex("[CONTROLLER NOT FOUND]");
  }

  public function testRouterUserDashboardGuest()
  {
    $path = '/users/dashboard';
    $_SERVER['PATH_INFO'] = $path;
    $_SERVER['REQUEST_METHOD'] = 'GET';
    Router::add(method: 'GET', path: $path, controller: UserController::class, function: 'dashboard', middleware: [MustLoginMiddleware::class]);
    Router::run();
    $baseurl = BASE_URL;
    $this->expectOutputRegex("[Location: $baseurl/users/login]");
  }
}
