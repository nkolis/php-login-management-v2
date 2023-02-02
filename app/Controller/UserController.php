<?php

namespace App\PHPLoginManagement\Controller;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Core\View;
use App\PHPLoginManagement\Exception\ValidateException;
use App\PHPLoginManagement\Model\UserRegisterRequest;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Service\UserService;
use Ramsey\Uuid\Uuid;

class UserController
{

  private UserService $userService;

  public function __construct()
  {
    $connection = Database::getConnection();
    $userRepository = new UserRepository($connection);
    $this->userService = new UserService($userRepository);
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
      $request->email = $_POST['email'];
      $request->name = $_POST['name'];
      $request->password = $_POST['password'];
      $this->userService->register($request);
      View::redirect('/users/login');
    } catch (ValidateException $e) {

      View::render('User/register', [
        'title' => 'Register new user',
        'error' => unserialize($e->getMessage())
      ]);
    }
  }
}
