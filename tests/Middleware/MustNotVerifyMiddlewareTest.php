<?php

namespace App\PHPLoginManagement\Middleware {
  require_once __DIR__ . '/../Helper/helper.php';

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

  class MustNotVerifyMiddlewareTest extends TestCase
  {
    private SessionService $sessionService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    private MustNotVerifyMiddleware $middleware;

    function setUp(): void
    {
      $connection = Database::getConnection();
      $this->userRepository = new UserRepository($connection);
      $this->sessionRepository = new SessionRepository($connection);
      $this->sessionService = new SessionService($this->userRepository, $this->sessionRepository);
      $this->middleware = new MustNotVerifyMiddleware;
      $verificationRepository = new VerificationUserRepository(Database::getConnection());
      $verificationRepository->deleteAll();
      $this->sessionRepository->deleteAll();
      $this->userRepository->deleteAll();
      $_COOKIE[SessionService::$COOKIE] = '';
      putenv("mode=test");
      $_SERVER['HTTP_USER_AGENT'] = 'mozilla';
      $_SERVER['REMOTE_ADDR'] = getenv("REMOTE_ADDR");
    }

    function testMustNotVerify()
    {
      $user = new User;
      $uuid = Uuid::uuid4();
      $user->id = $uuid->toString();
      $user->email = 'nurkholis010@gmail.com';
      $user->name = 'kholis';
      $user->verification_status = 'verified';
      $user->password = password_hash('kholis', PASSWORD_BCRYPT);
      $this->userRepository->save($user);
      $this->userRepository->update($user);

      $requesSession = new UserSessionRequest;
      $uuid = Uuid::uuid4();
      $requesSession->id = $uuid->toString();
      $requesSession->user_id = $user->id;
      $this->sessionService->create($requesSession);
      $_COOKIE[SessionService::$COOKIE] = $requesSession->id;


      $this->middleware->before();
      $baseurl = BASE_URL;
      $this->expectOutputRegex("[Location: $baseurl/users/dashboard]");
    }
  }
}
