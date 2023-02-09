<?php

namespace App\PHPLoginManagement\Controller;

use App\PHPLoginManagement\Config\BaseURL;
use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Core\View;
use App\PHPLoginManagement\Exception\ValidateException;
use App\PHPLoginManagement\Model\UserLoginRequest;
use App\PHPLoginManagement\Model\UserPasswordUpdateRequest;
use App\PHPLoginManagement\Model\UserProfileUpdateRequest;
use App\PHPLoginManagement\Model\UserRegisterRequest;
use App\PHPLoginManagement\Model\UserSessionRequest;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Service\SessionService;
use App\PHPLoginManagement\Service\UserService;
use Ramsey\Uuid\Uuid;

class UserController
{

  private UserService $userService;
  private SessionService $sessionService;

  public function __construct()
  {
    $connection = Database::getConnection();
    $userRepository = new UserRepository($connection);
    $sessionRepository = new SessionRepository($connection);
    $this->userService = new UserService($userRepository);
    $this->sessionService = new SessionService($userRepository, $sessionRepository);
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
}
