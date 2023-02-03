<?php

namespace App\PHPLoginManagement\Controller;

use App\PHPLoginManagement\Config\BaseURL;
use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\User;
use App\PHPLoginManagement\Model\UserSessionRequest;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Service\SessionService;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . '/../Helper/helper.php';

class UserControllerTest extends TestCase
{
  private UserController $userController;
  private SessionRepository $sessionRepository;
  private UserRepository $userRepository;
  private SessionService $sessionService;
  function setUp(): void
  {
    $connection = Database::getConnection();
    $this->userController = new UserController();
    $this->userRepository = new UserRepository($connection);
    $this->sessionRepository = new SessionRepository($connection);
    $this->sessionService = new SessionService($this->userRepository, $this->sessionRepository);
    putenv("mode=test");
    $this->sessionRepository->deleteAll();
    $this->userRepository->deleteAll();
    $_COOKIE[SessionService::$COOKIE] = '';
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

    $user = new User;
    $user->id = $this->uuid();
    $user->email = 'nurkholis@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $request = new UserSessionRequest();
    $request->id = $this->uuid();
    $request->user_id = $user->id;
    $this->sessionService->create($request);

    $_COOKIE[SessionService::$COOKIE] = $request->user_id;
    $this->userController->dashboard();
    $this->expectOutputRegex("[User dashboard]");
    $this->expectOutputRegex("[Profile]");
    $this->expectOutputRegex("[Password]");
    $this->expectOutputRegex("[Logout]");
    $this->expectOutputRegex("[Halo kholis, Selamat datang !]");
  }

  function testProfile()
  {
    $user = new User;
    $user->id = $this->uuid();
    $user->email = 'nurkholis@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $request = new UserSessionRequest();
    $request->id = $this->uuid();
    $request->user_id = $user->id;
    $this->sessionService->create($request);

    $_COOKIE[SessionService::$COOKIE] = $request->user_id;
    $this->userController->profile();
    $this->expectOutputRegex("[User profile]");
    $this->expectOutputRegex("[nurkholis@gmail.com]");
  }

  function testPostProfileUpdateSuccess()
  {
    $user = new User;
    $user->id = $this->uuid();
    $user->email = 'nurkholis@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $request = new UserSessionRequest();
    $request->id = $this->uuid();
    $request->user_id = $user->id;
    $this->sessionService->create($request);

    $_COOKIE[SessionService::$COOKIE] = $request->user_id;
    $_POST['email'] = 'setiawan@gmail.com';
    $_POST['name'] = 'kholis setiawan';
    $this->userController->postUpdateProfile();
    $baseurl = BaseURL::get();
    $this->expectOutputRegex("[Location: $baseurl/users/dashboard]");
  }

  function testPostProfileUpdateError()
  {
    $user = new User;
    $user->id = $this->uuid();
    $user->email = 'nurkholis@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $request = new UserSessionRequest();
    $request->id = $this->uuid();
    $request->user_id = $user->id;
    $this->sessionService->create($request);

    $_COOKIE[SessionService::$COOKIE] = $request->user_id;
    $_POST['email'] = ' ';
    $_POST['name'] = ' ';
    $this->userController->postUpdateProfile();

    $this->expectOutputRegex("[Invalid email]");
    $this->expectOutputRegex("[Name can't be empty]");
  }

  function testPostProfileEmailAlreadyRegistered()
  {
    $user = new User;
    $user->id = $this->uuid();
    $user_id = $user->id;
    $user->email = 'nurkholis@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
    $this->userRepository->save($user);
    $user->id = $this->uuid();
    $user->email = 'setiawan@gmail.com';
    $this->userRepository->save($user);

    $request = new UserSessionRequest();
    $request->id = $this->uuid();
    $request->user_id = $user_id;
    $this->sessionService->create($request);

    $_COOKIE[SessionService::$COOKIE] = $request->user_id;
    $_POST['email'] = 'setiawan@gmail.com';
    $_POST['name'] = 'setiawan';
    $this->userController->postUpdateProfile();

    $this->expectOutputRegex("[Email already registered]");
  }

  private function uuid(): string
  {
    $uuid = Uuid::uuid4();
    return $uuid->toString();
  }
}
