<?php

namespace App\PHPLoginManagement\Service;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\User;
use App\PHPLoginManagement\Model\UserRegisterRequest;
use App\PHPLoginManagement\Model\UserRegisterResponse;
use App\PHPLoginManagement\Repository\UserRepository;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UserServiceTest extends TestCase
{
  private UserRepository $userRepository;
  private UserService $userService;
  private User $user;

  function setUp(): void
  {
    $connection = Database::getConnection();
    $this->userRepository = new UserRepository($connection);
    $this->userService = new UserService($this->userRepository);

    $user = new User;
    $uuid = Uuid::uuid4();
    $user->id = $uuid->toString();
    $user->email = 'nurkholis@gmail.com';
    $user->name = 'Nur Kholis';
    $user->password = 'rahasia';
    $this->user = $user;
    $this->userRepository->deleteAll();
  }

  function testRegisterSuccess()
  {
    $user = $this->user;
    $request = new UserRegisterRequest;
    $request->id = $user->id;
    $request->name = $user->name;
    $request->email = $user->email;
    $request->password = $user->password;

    $response = $this->userService->register($request);
    $result = $this->userRepository->findById($request->id);

    $this->assertEquals($response::class, UserRegisterResponse::class);
    $this->assertEquals($request->id, $result->id);
    $this->assertEquals($request->email, $result->email);
    $this->assertEquals($request->name, $result->name);
    $this->assertEquals($request->password, $result->password);
  }

  function testRegisterExceptionValidateEmptyOrNull()
  {

    $this->expectException(Exception::class);

    $this->expectExceptionMessageMatches("[Id can't be empty]");

    $userNull = new User;
    $userEmpty = $this->user;
    $userEmpty->id = '';
    $userEmpty->email = '';
    $userEmpty->name = '';
    $userEmpty->password = '';


    $requestNull = new UserRegisterRequest;
    $requestNull->id = $userNull->id;
    $requestNull->name = $userNull->name;
    $requestNull->email = $userNull->email;
    $requestNull->password = $userNull->password;

    $this->userService->register($requestNull);


    $requestEmpty = new UserRegisterRequest;
    $requestEmpty->id = $userEmpty->id;
    $requestEmpty->name = $userEmpty->name;
    $requestEmpty->email = $userEmpty->email;
    $requestEmpty->password = $userEmpty->password;

    $this->userService->register($requestEmpty);
  }
}
