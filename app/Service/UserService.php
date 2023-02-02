<?php

namespace App\PHPLoginManagement\Service;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\User;
use App\PHPLoginManagement\Exception\ValidateException;
use App\PHPLoginManagement\Model\UserRegisterRequest;
use App\PHPLoginManagement\Model\UserRegisterResponse;
use App\PHPLoginManagement\Repository\UserRepository;
use Exception;

class UserService
{

  private UserRepository $userRepository;

  public function __construct($userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function register(UserRegisterRequest $request): UserRegisterResponse
  {

    $this->validateRegisterRequest($request);

    try {
      Database::beginTransaction();
      $user = new User;
      $user->id = $request->id;
      $user->email = $request->email;
      $user->name = $request->name;
      $user->password = $request->password;

      $this->userRepository->save($user);
      Database::commitTransaction();
      $response = new UserRegisterResponse;
      $response->user = $user;
      return $response;
    } catch (Exception $e) {
      Database::rollbackTransaction();
      throw $e;
    }
  }

  private function validateRegisterRequest(UserRegisterRequest $request): void
  {
    $errors = [];

    foreach ($request as $key => $value) {

      // validasi jika request kosong
      if ($value == null || $value = '') {
        $errors[$key] = ucwords($key) . " can't be empty";
      }
    }

    // validasi jika email tidak valid
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $request->email)) {
      $errors[$key] = 'Invalid email';
    }


    // validasi jika email sudah terdaftar
    $user = $this->userRepository->findByEmail($request->email);
    if ($user != null) {
      $errors['email'] = 'Email already registered';
    }

    // tangkap error exception
    if (!empty($errors)) {
      throw new ValidateException(serialize($errors));
    }
  }
}
