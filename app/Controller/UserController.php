<?php

namespace App\PHPLoginManagement\Controller;

use App\PHPLoginManagement\Config\BaseURL;
use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Core\View;
use App\PHPLoginManagement\Exception\ValidateException;
use App\PHPLoginManagement\Helper\Flasher;
use App\PHPLoginManagement\Model\UserLoginRequest;
use App\PHPLoginManagement\Model\UserPasswordUpdateRequest;
use App\PHPLoginManagement\Model\UserProfileUpdateRequest;
use App\PHPLoginManagement\Model\UserRegisterRequest;
use App\PHPLoginManagement\Model\UserSessionRequest;
use App\PHPLoginManagement\Model\UserVerificationRequest;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Repository\VerificationUserRepository;
use App\PHPLoginManagement\Service\SessionService;
use App\PHPLoginManagement\Service\UserService;
use App\PHPLoginManagement\Service\VerificationUserService;
use Exception;
use Ramsey\Uuid\Uuid;

class UserController
{

  private UserService $userService;
  private SessionService $sessionService;
  private VerificationUserService $vericationService;

  public function __construct()
  {
    $connection = Database::getConnection();
    $userRepository = new UserRepository($connection);
    $sessionRepository = new SessionRepository($connection);
    $verificationRepository = new VerificationUserRepository($connection);
    $this->userService = new UserService($userRepository);
    $this->sessionService = new SessionService($userRepository, $sessionRepository);
    $this->vericationService = new VerificationUserService($verificationRepository, $userRepository);
  }

  public function dashboard()
  {

    $current = $this->sessionService->currentSession();

    View::render('User/dashboard', [
      'title' => 'User dashboard',
      'user' => [
        'name' => $current->name ?? '',
        'verification_status' => $current->verification_status ?? ''
      ]
    ]);
  }

  public function register()
  {
    View::render('User/register', [
      'title' => 'Register new user'
    ]);
  }

  public function postRegister()
  {
    try {
      $request = new UserRegisterRequest;
      $uuid = Uuid::uuid4();
      $request->id = $uuid;
      $request->email = strip_tags($_POST['email']);
      $request->name = strip_tags($_POST['name']);
      $request->password = strip_tags($_POST['password']);
      $this->userService->register($request);

      View::redirect('/users/login');
    } catch (ValidateException $e) {

      View::render('User/register', [
        'title' => 'Register new user',
        'error' => unserialize($e->getMessage())
      ]);
    }
  }

  public function login()
  {
    View::render('User/login', [
      'title' => 'Login user'
    ]);
  }

  public function postLogin()
  {
    try {
      $requestUserLogin = new UserLoginRequest;
      $requestUserLogin->email = strip_tags($_POST['email']);
      $requestUserLogin->password = strip_tags($_POST['password']);

      $response = $this->userService->login($requestUserLogin);
      $sessionRequest = new UserSessionRequest;
      $sessionRequest->id = (Uuid::uuid4())->toString();
      $sessionRequest->user_id = $response->user->id;
      $this->sessionService->create($sessionRequest);
      View::render('User/login', [
        'title' => 'Login user',
        'swal' => json_encode([
          'icon' => 'success',
          'title' => 'Login Success',
          'timer' => 1000,
          'redirect-url' => BaseURL::get() . '/users/dashboard'
        ])
      ]);
    } catch (ValidateException $e) {

      View::render('User/login', [
        'title' => 'Login user',
        'error' => unserialize($e->getMessage())
      ]);
    }
  }

  public function profile()
  {
    $current = $this->sessionService->currentSession();
    View::render('User/profile', [
      'title' => 'User profile',
      'user' => [
        'id' => $current->user_id,
        'email' => $current->email,
        'name' => $current->name,
      ]
    ]);
  }

  public function postUpdateProfile()
  {
    try {
      $request = new UserProfileUpdateRequest;
      $current = $this->sessionService->currentSession();

      $request->id = $current->user_id;

      $request->email = strip_tags($_POST['email']);
      $request->name = strip_tags($_POST['name']);

      $this->userService->updateProfile($request);

      View::redirect('/users/dashboard');
    } catch (ValidateException $e) {

      View::render('User/profile', [
        'title' => 'User profile',
        'error' => unserialize($e->getMessage())
      ]);
    }
  }

  public function logout()
  {
    $this->sessionService->destroySession();
    View::redirect('/');
  }

  public function password()
  {
    $current = $this->sessionService->currentSession();
    View::render('User/password', [
      'title' => 'User password',
      'user' => [
        'id' => $current->user_id,
        'email' => $current->email,
        'name' => $current->name,
      ]
    ]);
  }

  public function postUpdatePassword()
  {
    try {
      $request = new UserPasswordUpdateRequest;
      $current = $this->sessionService->currentSession();

      $request->user_id = $current->user_id;
      $request->oldPassword = strip_tags($_POST['oldPassword']);
      $request->newPassword = strip_tags($_POST['newPassword']);

      $this->userService->updatePassword($request);

      View::redirect('/users/dashboard');
    } catch (ValidateException $e) {
      View::render('User/password', [
        'title' => 'User password',
        'user' => [
          'email' => $current->email,
          'name' => $current->name,
        ],
        'error' => unserialize($e->getMessage())
      ]);
    }
  }

  public function verification()
  {
    $user = $this->sessionService->currentSession();
    View::render('User/verification', [
      'title' => 'User verification',
      'user' => [
        'id' => $user->user_id,
        'email' => $user->email,
        'name' => $user->name,
      ]
    ]);
  }

  public function postVerification()
  {
    try {
      $user = $this->sessionService->currentSession();


      $request = new UserVerificationRequest;
      $request->user_id = $user->user_id;
      $request->code = strip_tags($_POST['code']);

      $this->vericationService->verifyUser($request);
      View::render('User/verification', [
        'title' => 'User verification',
        'swal' => json_encode([
          'icon' => 'success',
          'title' => 'Verification Success',
          'timer' => 1000,
          'redirect-url' => BaseURL::get() . '/users/dashboard'
        ])
      ]);
    } catch (Exception $e) {
      View::render('User/verification', [
        'title' => 'User verification',
        'user' => [
          'id' => $user->user_id,
          'email' => $user->email,
          'name' => $user->name,
        ],
        'error' => unserialize($e->getMessage())
      ]);
    }
  }

  public function postSendcode()
  {
    try {
      $user = $this->sessionService->currentSession();
      $request = new UserVerificationRequest;
      $request->user_id = $user->user_id;
      $this->vericationService->sendVerificationCode($request);
      Flasher::set([
        'success' => "Code has been sent to <b>{$user->email}</b>, please check your email box!"
      ]);
      View::redirect('/users/verification');
    } catch (Exception $e) {
      View::render('User/verification', [
        'title' => 'User verification',
        'user' => [
          'id' => $user->user_id,
          'email' => $user->email,
          'name' => $user->name,
        ],
        'error' => ["verification" => $e->getMessage()]
      ]);
    }
  }

  public function passwordReset()
  {
    View::render('User/password_reset', [
      'title' => 'User password',
    ]);
  }

  public function postPasswordReset()
  {
    try {
      $response = $this->userService->sendRequestPasswordReset(strip_tags($_POST['email']));
      $userSessionRequest = new UserSessionRequest;
      $uuid = Uuid::uuid4();
      $userSessionRequest->id = $uuid->toString();
      $userSessionRequest->user_id = $response->id;
      $this->sessionService->create($userSessionRequest, "PLM-RESET-PASSWORD");

      $userVerificationRequest = new UserVerificationRequest;
      $userVerificationRequest->user_id = $response->id;
      $this->vericationService->sendVerificationCode($userVerificationRequest);

      Flasher::set([
        'success' => "Code has been sent to <b>{$response->email}</b>, please check your email box!"
      ]);
      View::redirect('/users/password_reset/verify');
    } catch (Exception $e) {
      View::render('User/password_reset', [
        'title' => 'User password',
        'error' => unserialize($e->getMessage())
      ]);
    }
  }

  public function passwordResetVerify()
  {
    View::render('User/password_reset_verify', [
      'title' => 'User password',
    ]);
  }

  public function postPasswordResetVerify()
  {
  }
}
