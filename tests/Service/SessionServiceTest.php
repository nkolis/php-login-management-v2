<?php

namespace App\PHPLoginManagement\Service {
  require_once __DIR__ . "/../Helper/helper.php";

  use App\PHPLoginManagement\Config\Database;
  use App\PHPLoginManagement\Entity\Session;
  use App\PHPLoginManagement\Entity\User;
  use App\PHPLoginManagement\Model\UserSessionRequest;
  use App\PHPLoginManagement\Repository\SessionRepository;
  use App\PHPLoginManagement\Repository\UserRepository;
  use App\PHPLoginManagement\Repository\VerificationUserRepository;
  use Exception;
  use PHPUnit\Framework\TestCase;
  use Ramsey\Uuid\Nonstandard\Uuid;

  class SessionServiceTest extends TestCase
  {
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    private SessionService $sessionService;

    function setUp(): void
    {
      $connection = Database::getConnection();

      $this->userRepository = new UserRepository($connection);
      $this->sessionRepository = new SessionRepository($connection);
      $this->sessionService = new SessionService($this->userRepository, $this->sessionRepository);
      $verificationRepository = new VerificationUserRepository(Database::getConnection());
      $verificationRepository->deleteAll();
      $this->sessionRepository->deleteAll();
      $this->userRepository->deleteAll();
      $_COOKIE[SessionService::$COOKIE] = '';
      putenv('HTTP_USER_AGENT=Mozilla');
      putenv('REMOTE_ADDR=127.0.0.1');
      $_SERVER['HTTP_USER_AGENT'] = 'mozilla';
      $_SERVER['REMOTE_ADDR'] = getenv("REMOTE_ADDR");
    }

    function testCreateSessionSuccess()
    {
      $user = new User;
      $user->id = $this->uuid();
      $user->email = 'nurkholis@gmail.com';
      $user->name = 'khols';
      $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
      $this->userRepository->save($user);

      $request = new UserSessionRequest();
      $request->id = $this->uuid();
      $request->user_id = $user->id;

      $this->sessionService->create($request);

      $_COOKIE[SessionService::$COOKIE] = $request->id;

      $result = $this->sessionRepository->findById($request->id);

      $this->assertEquals($request->id, $result->id);
      $this->assertEquals($request->user_id, $result->user_id);

      $this->expectOutputRegex("[PLM-SESSION, $request->id]");
    }

    function testCreateSessionError()
    {
      $this->expectExceptionMessage('Failed create session user not found');
      $request = new UserSessionRequest();
      $request->id = $this->uuid();
      $request->user_id = 'nofound';
      $this->sessionService->create($request);
    }

    function testFindCurrentSession()
    {
      $_COOKIE[SessionService::$COOKIE] = '';
      $user = new User;
      $user->id = $this->uuid();
      $user->email = 'nurkholis123@gmail.com';
      $user->name = 'khols';
      $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
      $this->userRepository->save($user);


      $request = new UserSessionRequest();
      $request->id = $this->uuid();
      $request->user_id = $user->id;
      $this->sessionService->create($request);

      $_COOKIE[SessionService::$COOKIE] = $request->id;
      $session = $this->sessionRepository->findById($request->id);
      $result = $this->sessionService->currentSession();

      $this->assertEquals($session->user_id, $result->user_id);
      $this->expectOutputRegex("[PLM-SESSION, $request->id]");
    }

    function testFindCurrentSessionNull()
    {
      unset($_COOKIE[SessionService::$COOKIE]);
      $result = $this->sessionService->currentSession();
      $this->assertNull($result);
    }

    function testSessionDestroy()
    {
      $_COOKIE[SessionService::$COOKIE] = '';
      $user = new User;
      $user->id = $this->uuid();
      $user->email = 'nurkholis123@gmail.com';
      $user->name = 'khols';
      $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
      $this->userRepository->save($user);


      $request = new UserSessionRequest();
      $request->id = $this->uuid();
      $request->user_id = $user->id;
      $this->sessionService->create($request);

      $_COOKIE[SessionService::$COOKIE] = $request->id;

      $this->sessionService->destroySession();

      $this->expectOutputRegex("[PLM-SESSION, ]");
    }


    private function uuid(): string
    {
      $uuid = Uuid::uuid4();
      return $uuid->toString();
    }
  }
}
