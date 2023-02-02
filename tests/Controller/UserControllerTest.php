<?php

namespace App\PHPLoginManagement\Controller;

use App\PHPLoginManagement\Config\BaseURL;
use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\User;
use App\PHPLoginManagement\Repository\UserRepository;
use DOMDocument;
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../Helper/helper.php';

class UserControllerTest extends TestCase
{
  private UserController $userController;
  private UserRepository $userRepository;
  function setUp(): void
  {
    $connection = Database::getConnection();
    $this->userController = new UserController();
    $this->userRepository = new UserRepository($connection);
    putenv("mode=test");
    $this->userRepository->deleteAll();
  }

  function testRegister()
  {
    $this->userController->register();
    $this->expectOutputRegex("[Register new user]");
    $this->expectOutputRegex("[Name]");
    $this->expectOutputRegex("[Email]");
    $this->expectOutputRegex("[Password]");
  }

  function testPostRegisterSuccess()
  {
    $_POST['email'] = 'nurkholis@gmail.com';
    $_POST['name'] = 'kholis';
    $_POST['password'] = 'rahasia';

    $this->userController->postRegister();

    $result = $this->userRepository->findByEmail($_POST['email']);

    self::assertEquals($_POST['email'], $result->email);
    self::assertEquals($_POST['name'], $result->name);
    self::assertTrue(password_verify($_POST['password'], $result->password));
    $baseurl = BaseURL::get();
    $this->expectOutputRegex("[Location: $baseurl/users/login]");
  }

  function testRegisterValidationErrorAllEmpty()
  {
    $_POST['email'] = ' ';
    $_POST['name'] = ' ';
    $_POST['password'] = '';

    $this->userController->postRegister();

    $this->expectOutputRegex("[Invalid email]");
    $this->expectOutputRegex("/Name can't be emty/");
    $this->expectOutputRegex("[Password can't be empty]");
  }

  function testRegisterValidationErrorEmpty()
  {
    $_POST['email'] = ' ';
    $_POST['name'] = 'kholis';
    $_POST['password'] = '';

    $this->userController->postRegister();
    $this->expectOutputRegex("[Invalid email]");
    $this->expectOutputRegex("[Password can't be empty]");
  }

  function testRegisterValidationErrorDuplicate()
  {
    $_POST['email'] = 'nurkholis@gmail.com';
    $_POST['name'] = 'kholis';
    $_POST['password'] = 'rahasia';

    $this->userController->postRegister();

    $_POST['email'] = 'nurkholis@gmail.com';
    $_POST['name'] = 'kholis';
    $_POST['password'] = 'rahasia';

    $this->userController->postRegister();
    $this->expectOutputRegex("[Email already registered]");
  }

  function testLogin()
  {
    $this->userController->login();

    $this->expectOutputRegex("[Login user]");
    $this->expectOutputRegex("[Email]");
    $this->expectOutputRegex("[Password]");
  }

  function testLoginValidationErrorEmpty()
  {
    $_POST['email'] = 'ndfd';

    $_POST['password'] = '';

    $this->userController->postRegister();
    $this->expectOutputRegex("[Invalid email]");
    $this->expectOutputRegex("[Password can't be empty]");
  }

  function testLoginValidationErrorWrongPassword()
  {

    $_POST['email'] = 'nurkholis@gmail.com';
    $_POST['name'] = 'kholis';
    $_POST['password'] = 'rahasia';

    $this->userController->postRegister();
    $_POST['email'] = 'nurkholis@gmail.com';
    $_POST['password'] = 'salah';
    $this->userController->postLogin();
    $this->expectOutputRegex("[Incorrect email or password]");
  }

  function testLoginSuccess()
  {
    $_POST['email'] = 'nurkholis@gmail.com';
    $_POST['name'] = 'kholis';
    $_POST['password'] = 'rahasia';

    $this->userController->postRegister();
    $this->userController->postLogin();
    $baseurl = BaseURL::get();
    $this->expectOutputRegex("[Location: $baseurl/users/dashboard]");
  }

  function testDashboard()
  {
    $this->userController->dashboard();
    $this->expectOutputRegex("[User dashboard]");
    $this->expectOutputRegex("[Profile]");
    $this->expectOutputRegex("[Password]");
    $this->expectOutputRegex("[Logout]");
  }
}
