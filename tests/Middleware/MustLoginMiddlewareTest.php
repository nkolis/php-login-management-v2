<?php

namespace App\PHPLoginManagement\Middleware {

  require_once __DIR__ . '/../Helper/helper.php';

  use App\PHPLoginManagement\Config\BaseURL;
  use App\PHPLoginManagement\Config\Database;
  use App\PHPLoginManagement\Entity\User;
  use App\PHPLoginManagement\Model\UserSessionRequest;
  use App\PHPLoginManagement\Repository\SessionRepository;
  use App\PHPLoginManagement\Repository\UserRepository;
  use App\PHPLoginManagement\Service\SessionService;
  use PHPUnit\Framework\TestCase;
  use Ramsey\Uuid\Uuid;


  class MustLoginMiddlewareTest extends TestCase
  {
    private SessionService $sessionService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    private MustLoginMiddleware $middleware;

    function setUp(): void
    {
      $connection = Database::getConnection();
      $this->userRepository = new UserRepository($connection);
      $this->sessionRepository = new SessionRepository($connection);
      $this->sessionService = new SessionService($this->userRepository, $this->sessionRepository);
      $this->middleware = new MustLoginMiddleware;
      $this->sessionRepository->deleteAll();
      $this->userRepository->deleteAll();
      $_COOKIE[SessionService::$COOKIE] = '';
      putenv("mode=test");
    }

    function testMustLoginGuest()
    {

      $this->middleware->before();
      $baseurl = BaseURL::get();
      $this->expectOutputRegex("[Location: $baseurl/users/login]");
    }


    function testMustLoginBeforeLogin()
    {
      $user = new User;
      $uuid = Uuid::uuid4();
      $user->id = $uuid->toString();
      $user->email = 'nurkholis@gmail.com';
      $user->name = 'kholis';
      $user->password = password_hash('kholis', PASSWORD_BCRYPT);
      $this->userRepository->save($user);

      $requesSession = new UserSessionRequest;
      $uuid = Uuid::uuid4();
      $requesSession->id = $uuid->toString();
      $requesSession->user_id = $user->id;
      $this->sessionService->create($requesSession);
      $_COOKIE[SessionService::$COOKIE] = $requesSession->id;

      $this->middleware->before();

      $this->expectOutputRegex("[]");
    }
  }
}
