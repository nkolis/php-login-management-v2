<?php

namespace App\PHPLoginManagement\Service;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\User;
use App\PHPLoginManagement\Exception\ValidateException;
use App\PHPLoginManagement\Model\UserRegisterRequest;
use App\PHPLoginManagement\Model\UserRegisterResponse;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UserServiceTest extends TestCase
{
  private UserRepository $userRepository;
  private SessionRepository $sessionRepository;
  private UserService $userService;
  private User $user;

  function setUp(): void
  {
    $connection = Database::getConnection();
    $this->userRepository = new UserRepository($connection);
    $this->sessionRepository = new SessionRepository($connection);
    $this->userService = new UserService($this->userRepository);

    $user = new User;
    $uuid = Uuid::uuid4();
    $user->id = $uuid->toString();
    $user->email = 'nurkholis@gmail.com';
    $user->name = 'Nur Kholis';
    $user->password = 'rahasia';
    $this->user = $user;
    $this->sessionRepository->deleteAll();
    $this->userRepository->deleteAll();
  }

  function testRegisterSuccess()
  {
    $user = $this->user;
    $request = new UserRegisterRequest;
    $request->id = $user->id;
    $request->name = $user->name;
    $request->email = "$user->email";
    $request->password = $user->password;

    $response = $this->userService->register($request);
    $result = $this->userRepository->findById($request->id);

    $this->assertEquals($response::class, UserRegisterResponse::class);
    $this->assertEquals($request->id, $result->id);
    $this->assertEquals($request->email, $result->email);
    $this->assertEquals($request->name, $result->name);
    $this->assertTrue(password_verify($request->password, $result->password));
  }

  function testRegisterExceptionValidateEmpty()
  {

    $this->expectException(ValidateException::class);

    $this->expectExceptionMessageMatches("[can't be empty]");
    $this->expectExceptionMessageMatches("[Invalid email]");

    $userEmpty = $this->user;
    $userEmpty->id = '    ';
    $userEmpty->email = '   ';
    $userEmpty->name = ' ';
    $userEmpty->password = ' ';


    $requestEmpty = new UserRegisterRequest;
    $requestEmpty->id = $userEmpty->id;
    $requestEmpty->name = $userEmpty->name;
    $requestEmpty->email = $userEmpty->email;
    $requestEmpty->password = $userEmpty->password;

    $this->userService->register($requestEmpty);
  }

  function testRegisterExceptionValidateNull()
  {

    $this->expectException(ValidateException::class);

    $this->expectExceptionMessageMatches("[can't be empty]");
    $this->expectExceptionMessageMatches("[Invalid email]");
    $userNull = new User;
    $requestNull = new UserRegisterRequest;
    $requestNull->id = $userNull->id;
    $requestNull->name = $userNull->name;
    $requestNull->email = $userNull->email;
    $requestNull->password = $userNull->password;

    $this->userService->register($requestNull);
  }
}
