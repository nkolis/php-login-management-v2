<?php

namespace App\PHPLoginManagement\Controller;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Core\View;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Service\UserService;

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
  }
}
