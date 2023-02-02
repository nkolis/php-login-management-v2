<?php

namespace App\PHPLoginManagement\Controller;

use App\PHPLoginManagement\Config\Database;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
  private UserController $userController;
  function setUp(): void
  {
    $connection = Database::getConnection();
    $this->userController = new UserController();
  }

  function testRegister()
  {

    $this->userController->register();

    $this->expectOutputRegex("[xxx]");
  }
}
