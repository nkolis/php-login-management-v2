<?php

namespace App\PHPLoginManagement\Controller;

use App\PHPLoginManagement\Config\BaseURL;
use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\User;
use App\PHPLoginManagement\Entity\VerificationUser;
use App\PHPLoginManagement\Model\UserSessionRequest;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Repository\VerificationUserRepository;
use App\PHPLoginManagement\Service\SessionService;
use App\PHPLoginManagement\Service\VerificationUserService;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use ReflectionClass;

require_once __DIR__ . '/../Helper/helper.php';

class UserControllerTest extends TestCase
{
  private UserController $userController;
  private SessionRepository $sessionRepository;
  private UserRepository $userRepository;
  private SessionService $sessionService;
  private VerificationUserRepository $verificationRepository;
  private VerificationUserService $verificationService;
  function setUp(): void
  {
    $connection = Database::getConnection();
    $this->userController = new UserController();
    $this->userRepository = new UserRepository($connection);
    $this->sessionRepository = new SessionRepository($connection);
    $this->sessionService = new SessionService($this->userRepository, $this->sessionRepository);
    putenv("mode=test");
    $this->verificationRepository = new VerificationUserRepository($connection);
    $this->verificationService = new VerificationUserService($this->verificationRepository, $this->userRepository);
    $this->verificationRepository->deleteAll();
    $this->sessionRepository->deleteAll();
    $this->userRepository->deleteAll();
    $_COOKIE[SessionService::$COOKIE] = '';
    $_SERVER['HTTP_USER_AGENT'] = 'mozilla';
    $_SERVER['REMOTE_ADDR'] = getenv("REMOTE_ADDR");

    $class = new ReflectionClass($this->verificationService);
    $class->setStaticPropertyValue('expire_code', 60 * 10);
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
    $this->expectOutputRegex("[Logi user]");
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
    $this->expectOutputRegex("[User login]");
    $this->expectOutputRegex("[Login Success]");
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
    $this->userController->dashboard();
    $this->expectOutputRegex("[User dashboard]");
    $this->expectOutputRegex("[Profile]");
    $this->expectOutputRegex("[Password]");
    $this->expectOutputRegex("[Logout]");
    $this->expectOutputRegex("[Halo kholis, Selamat datang!]");
  }


  function testDashboardUserUnverified()
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
    $this->userController->dashboard();
    $this->expectOutputRegex("[Please verify your account!]");
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
    $_POST['email'] = 'setiawan@gmail.com';
    $_POST['name'] = 'setiawan';
    $this->userController->postUpdateProfile();

    $this->expectOutputRegex("[User profile]");
    $this->expectOutputRegex("[Email already registered]");
  }

  function testPassword()
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
    $this->userController->password();
    $this->expectOutputRegex("[User password]");
    $this->expectOutputRegex("[nurkholis@gmail.com]");
  }

  function testPostUpdatePasswordSuccess()
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
    $_POST['oldPassword'] = 'rahasia';
    $_POST['newPassword'] = '123';
    $this->userController->postUpdatePassword();
    $baseurl = BaseURL::get();
    $this->expectOutputRegex("[Location: $baseurl/users/dashboard]");
  }


  function testPostUpdatePasswordError()
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
    $_POST['oldPassword'] = ' ';
    $_POST['newPassword'] = ' ';
    $this->userController->postUpdatePassword();
    $this->expectOutputRegex("[Old password can't be empty]");
    $this->expectOutputRegex("[New password can't be empty]");
  }


  function testPostUpdatePasswordWrongOldPassword()
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
    $_POST['oldPassword'] = 'salah';
    $_POST['newPassword'] = '123';
    $this->userController->postUpdatePassword();
    $baseurl = BaseURL::get();
    $this->expectOutputRegex("[User password]");
    $this->expectOutputRegex("[Old password is wrong]");
  }


  function testVerification()
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
    $this->userController->verification();
    $this->expectOutputRegex("[User verification]");
    $this->expectOutputRegex("[Enter code here]");
  }

  function testPostVerificationValidateEmpty()
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
    $_POST['code'] = '';
    $this->userController->postVerification();
    $this->expectOutputRegex("[Code can't be empty]");
  }


  function testPostVerificationCodeNotSend()
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;
    $_POST['code'] = '123456';
    $this->userController->postVerification();
    $this->expectOutputRegex("[Please send code and check your mail box!]");
  }

  function testPostVerificationCodeExpired()
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;

    $verification = new VerificationUser;
    $verification->user_id = $user->id;
    $verification->code = '123456';
    $this->verificationRepository->save($verification);


    $class = new ReflectionClass($this->verificationService);
    $class->setStaticPropertyValue('expire_code', -1);
    $_POST['code'] = '123456';
    $this->userController->postVerification();
    $this->expectOutputRegex("[Your code verification is expired, send code again!]");
  }

  function testPostVerificationWrongCode()
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;

    $verification = new VerificationUser;
    $verification->user_id = $user->id;
    $verification->code = '123456';
    $this->verificationRepository->save($verification);


    $_POST['code'] = 'salah';
    $this->userController->postVerification();
    $this->expectOutputRegex("[Incorrect code verification]");
  }

  function testPostVerificationSuccess()
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

    $_COOKIE[SessionService::$COOKIE] = $request->id;

    $verification = new VerificationUser;
    $verification->user_id = $user->id;
    $verification->code = '123456';
    $this->verificationRepository->save($verification);


    $_POST['code'] = '123456';
    $this->userController->postVerification();
    $this->expectOutputRegex("[Verification Success]");
    $this->assertNull($this->verificationRepository->findByUserId($user->id));
  }

  public function testPostSendCodeSuccess()
  {
    $user = new User;
    $user->id = $this->uuid();
    $user->email = 'nurkholis010@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $request = new UserSessionRequest();
    $request->id = $this->uuid();
    $request->user_id = $user->id;
    $this->sessionService->create($request);

    $_COOKIE[SessionService::$COOKIE] = $request->id;
    $this->userController->postSendcode();
    $this->expectOutputRegex("[Code has been sent to <b>{$user->email}</b>, please check your email box!]");
  }

  public function testPasswordReset()
  {
    $this->userController->passwordReset();
    $this->expectOutputRegex("[User password]");
    $this->expectOutputRegex("[Email]");
  }

  public function testSendPasswordResetEmailNotRegistered()
  {
    $_POST['email'] = 'notfound';
    $this->userController->postPasswordReset();
    $this->expectOutputRegex("[Email not registered!]");
  }

  public function testSendPasswordResetSuccess()
  {
    $user = new User;
    $user->id = $this->uuid();
    $user->email = 'nurkholis010@gmail.com';
    $user->name = 'kholis';
    $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
    $this->userRepository->save($user);
    $_POST['email'] = $user->email;
    $this->userController->postPasswordReset();
    $baseurl = BaseURL::get();
    $this->expectOutputRegex("[Location: $baseurl/users/password_reset/verify]");
  }

  private function uuid(): string
  {
    $uuid = Uuid::uuid4();
    return $uuid->toString();
  }
}
