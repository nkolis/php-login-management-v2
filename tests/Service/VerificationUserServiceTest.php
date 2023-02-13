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
use Exception;
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
    $this->verificationUserService = new VerificationUserService($this->verificationUserRepository, $this->userRepository);
    $sessionRepository = new SessionRepository($connection);
    $this->verificationUserRepository->deleteAll();
    $sessionRepository->deleteAll();
    $this->userRepository->deleteAll();

    $class = new ReflectionClass($this->verificationUserService);
    $class->setStaticPropertyValue('expire_code', 60 * 10);
    putenv("mode=test");
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
    $this->getMethod('generateCodeVerification')->invoke($this->verificationUserService, $user_verification);

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
    $this->getMethod('generateCodeVerification')->invoke($this->verificationUserService, $user_verification);
    $this->getMethod('generateCodeVerification')->invoke($this->verificationUserService, $user_verification);

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
    $this->getMethod('generateCodeVerification')->invoke($this->verificationUserService, $user_verification);

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
    $this->getMethod('generateCodeVerification')->invoke($this->verificationUserService, $user_verification);

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
    $this->getMethod('generateCodeVerification')->invoke($this->verificationUserService, $user_verification);
    $this->getMethod('generateCodeVerification')->invoke($this->verificationUserService, $user_verification);

    $class = new ReflectionClass($this->verificationUserService);
    $class->setStaticPropertyValue('expire_code', -1);

    $method = $class->getMethod('currentCodeVerification');
    $result = $method->invoke($this->verificationUserService, $user->id);
  }

  // public function testCurrentCodeVerificationUpdateNotExpired()
  // {
  // }

  public function testSendVerificationCodeSuccess()
  {
    $user = new User;
    $user->id = Uuid::uuid4()->toString();
    $user->email = 'nurkholis010@gmail.com';
    $user->name = 'Nur Kholis';
    $user->password = password_hash('123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $user_verification = new UserVerificationRequest;
    $user_verification->user_id = $user->id;
    $this->verificationUserService->sendVerificationCode($user_verification);
    $this->expectOutputRegex("[]");
  }

  public function testSendVerificationCodeError()
  {
    $this->expectException(Exception::class);
    $user = new User;
    $user->id = Uuid::uuid4()->toString();
    $user->email = 'nurkholis';
    $user->name = 'Nur Kholis';
    $user->password = password_hash('123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $user_verification = new UserVerificationRequest;
    $user_verification->user_id = $user->id;
    $this->verificationUserService->sendVerificationCode($user_verification);
  }

  public function testVerifyUserSuccess()
  {
    $user = new User;
    $user->id = Uuid::uuid4()->toString();
    $user->email = 'nurkholis010@gmail.com';
    $user->name = 'Nur Kholis';
    $user->password = password_hash('123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $verification = new VerificationUser;
    $verification->user_id = $user->id;
    $verification->code = '123456';
    $this->verificationUserRepository->save($verification);

    $user_verification = new UserVerificationRequest;
    $user_verification->user_id = $user->id;
    $user_verification->code = '123456';
    $this->verificationUserService->verifyUser($user_verification);

    $result = $this->userRepository->findById($user->id);
    $this->assertEquals('verified', $result->verification_status);
    $this->assertNull($this->verificationUserRepository->findByUserId($user->id));
  }

  public function testVerifyUserWrongCode()
  {
    $this->expectExceptionMessage("Incorrect code verification");
    $user = new User;
    $user->id = Uuid::uuid4()->toString();
    $user->email = 'nurkholis010@gmail.com';
    $user->name = 'Nur Kholis';
    $user->password = password_hash('123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $verification = new VerificationUser;
    $verification->user_id = $user->id;
    $verification->code = '123456';
    $this->verificationUserRepository->save($verification);

    $user_verification = new UserVerificationRequest;
    $user_verification->user_id = $user->id;
    $user_verification->code = 'salah';
    $this->verificationUserService->verifyUser($user_verification);
  }

  public function testVerifyUserExpiredCode()
  {
    $this->expectExceptionMessage("Your code verification is expired, send code again!");
    $user = new User;
    $user->id = Uuid::uuid4()->toString();
    $user->email = 'nurkholis010@gmail.com';
    $user->name = 'Nur Kholis';
    $user->password = password_hash('123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $verification = new VerificationUser;
    $verification->user_id = $user->id;
    $verification->code = '123456';
    $this->verificationUserRepository->save($verification);

    $user_verification = new UserVerificationRequest;
    $user_verification->user_id = $user->id;
    $user_verification->code = '123456';

    $class = new ReflectionClass($this->verificationUserService);
    $class->setStaticPropertyValue('expire_code', -1);
    $this->verificationUserService->verifyUser($user_verification);
  }
}
