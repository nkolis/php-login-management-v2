<?php

namespace App\PHPLoginManagement\Service;

date_default_timezone_set("Asia/Jakarta");

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\User;
use App\PHPLoginManagement\Entity\VerificationUser;
use App\PHPLoginManagement\Model\UserVerificationRequest;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Repository\VerificationUserRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use ReflectionClass;

class VerificationUserServiceTest extends TestCase
{

  private VerificationUserRepository $verificationUserRepository;
  private VerificationUserService $verificationUserService;
  private UserRepository $userRepository;

  function setUp(): void
  {
    $connection = Database::getConnection();
    $this->userRepository = new UserRepository($connection);
    $this->verificationUserRepository = new VerificationUserRepository($connection);
    $this->verificationUserService = new VerificationUserService($this->verificationUserRepository);
    $sessionRepository = new SessionRepository($connection);
    $this->verificationUserRepository->deleteAll();
    $sessionRepository->deleteAll();
    $this->userRepository->deleteAll();
  }

  protected function getMethod($name)
  {
    $class = new ReflectionClass($this->verificationUserService);
    $method = $class->getMethod($name);
    $method->setAccessible(true);
    return $method;
  }

  public function testRandCode()
  {
    $result = $this->getMethod('randCode');
    $result = $result->invoke($this->verificationUserService, 6);
    self::assertIsString($result);
    self::assertEquals(6, strlen($result));
  }

  public function testGenerateCodeVericationSaveSuccess()
  {
    $user = new User;
    $user->id = Uuid::uuid4()->toString();
    $user->email = 'nkholis@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $user_verification = new UserVerificationRequest;
    $user_verification->user_id = $user->id;
    $this->verificationUserService->generateCodeVerification($user_verification);

    $result = $this->verificationUserRepository->findByUserId($user->id);

    $this->assertEquals($user->id, $result->user_id);
    $this->assertTrue(strlen($result->code) >= 6);
    $this->assertNull($result->updated_at);
  }

  public function testGenerateCodeVericationUpdateSuccess()
  {
    $user = new User;
    $user->id = Uuid::uuid4()->toString();
    $user->email = 'nkholis@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $user_verification = new UserVerificationRequest;
    $user_verification->user_id = $user->id;
    $this->verificationUserService->generateCodeVerification($user_verification);
    $this->verificationUserService->generateCodeVerification($user_verification);

    $result = $this->verificationUserRepository->findByUserId($user->id);

    $this->assertEquals($user->id, $result->user_id);
    $this->assertTrue(strlen($result->code) >= 6);
    $this->assertNotNull($result->updated_at);
  }

  public function testCurrentCodeVerificationNotFound()
  {
    $this->expectExceptionMessage("Klik send code and check your mail box!");
    $this->verificationUserService->currentCodeVerification('notfound');
  }

  public function testCurrentCodeVerificationFound()
  {
    $user = new User;
    $user->id = Uuid::uuid4()->toString();
    $user->email = 'nkholis@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $user_verification = new UserVerificationRequest;
    $user_verification->user_id = $user->id;
    $this->verificationUserService->generateCodeVerification($user_verification);

    $result = $this->verificationUserService->currentCodeVerification($user->id);
    $this->assertEquals(VerificationUser::class, $result::class);
    $this->assertEquals($user->id, $result->user_id);
    $this->assertTrue(strlen($result->code) >= 6);
    $this->assertNull($result->updated_at);
  }

  public function testCurrentCodeVerificationFirstExpired()
  {
    $this->expectExceptionMessage("Your code verification is expired, send code again!");
    $user = new User;
    $user->id = Uuid::uuid4()->toString();
    $user->email = 'nkholis@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $user_verification = new UserVerificationRequest;
    $user_verification->user_id = $user->id;
    $this->verificationUserService->generateCodeVerification($user_verification);

    $class = new ReflectionClass($this->verificationUserService);
    $class->setStaticPropertyValue('expire_code', -1);

    $method = $class->getMethod('currentCodeVerification');
    $result = $method->invoke($this->verificationUserService, $user->id);
  }

  public function testCurrentCodeVerificationUpdateExpired()
  {
    $this->expectExceptionMessage("Your code verification is expired, send code again!");
    $user = new User;
    $user->id = Uuid::uuid4()->toString();
    $user->email = 'nkholis@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $user_verification = new UserVerificationRequest;
    $user_verification->user_id = $user->id;
    $this->verificationUserService->generateCodeVerification($user_verification);
    $this->verificationUserService->generateCodeVerification($user_verification);

    $class = new ReflectionClass($this->verificationUserService);
    $class->setStaticPropertyValue('expire_code', -1);

    $method = $class->getMethod('currentCodeVerification');
    $result = $method->invoke($this->verificationUserService, $user->id);
  }
}
