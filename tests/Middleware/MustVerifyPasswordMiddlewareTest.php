<?php

namespace App\PHPLoginManagement\Middleware;

use PHPUnit\Framework\TestCase;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\User;
use App\PHPLoginManagement\Entity\VerificationUser;
use App\PHPLoginManagement\Model\UserSessionRequest;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Repository\VerificationUserRepository;
use App\PHPLoginManagement\Service\SessionService;
use Ramsey\Uuid\Uuid;

class MustVerifyPasswordMiddlewareTest extends TestCase
{
  private SessionService $sessionService;
  private UserRepository $userRepository;
  private SessionRepository $sessionRepository;
  private VerificationUserRepository $verificationRepository;
  private MustVerifyPasswordMiddleware $middleware;

  function setUp(): void
  {
    $connection = Database::getConnection();
    $this->userRepository = new UserRepository($connection);
    $this->sessionRepository = new SessionRepository($connection);
    $this->sessionService = new SessionService($this->userRepository, $this->sessionRepository);
    $this->middleware = new MustVerifyPasswordMiddleware;
    $this->verificationRepository = new VerificationUserRepository(Database::getConnection());
    $this->verificationRepository->deleteAll();
    $this->sessionRepository->deleteAll();
    $this->userRepository->deleteAll();
    $_COOKIE[SessionService::$COOKIE] = '';
    $_COOKIE["PLM-RESET-PASSWORD"] = '';
    putenv("mode=test");
    $_SERVER['HTTP_USER_AGENT'] = 'mozilla';
    $_SERVER['REMOTE_ADDR'] = getenv("REMOTE_ADDR");
    $_SERVER['REQUEST_URI'] = '';
  }

  function testBeforeNotSendPasswordReset()
  {
    $this->middleware->before();
    $baseurl = BASE_URL;
    $this->expectOutputRegex("[$baseurl/users/password_reset]");
  }

  function testBeforeSendPasswordReset()
  {
    $user = new User;
    $uuid = Uuid::uuid4();
    $user->id = $uuid->toString();
    $user->email = 'nurkholis010@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('kholis', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $requesSession = new UserSessionRequest;
    $uuid = Uuid::uuid4();
    $requesSession->id = $uuid->toString();
    $requesSession->user_id = $user->id;
    $this->sessionService->create($requesSession, 'PLM-RESET-PASSWORD');
    $_COOKIE["PLM-RESET-PASSWORD"] = $requesSession->id;

    $verification_user = new VerificationUser;
    $verification_user->user_id = $user->id;
    $verification_user->code = '123456';

    $this->verificationRepository->save($verification_user);
    $this->middleware->before();

    $this->expectOutputRegex("[]");
  }

  function testBeforeSendPasswordCode()
  {
    $user = new User;
    $uuid = Uuid::uuid4();
    $user->id = $uuid->toString();
    $user->email = 'nurkholis010@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('kholis', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $requesSession = new UserSessionRequest;
    $uuid = Uuid::uuid4();
    $requesSession->id = $uuid->toString();
    $requesSession->user_id = $user->id;
    $this->sessionService->create($requesSession, 'PLM-RESET-PASSWORD');
    $_COOKIE["PLM-RESET-PASSWORD"] = $requesSession->id;


    $_SERVER['REQUEST_URI'] = '/users/password_reset/change';
    $this->middleware->before();
    $baseurl = BASE_URL;
    $this->expectOutputRegex("[]");
  }
}
