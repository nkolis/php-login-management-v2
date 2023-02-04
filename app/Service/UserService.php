<?php

namespace App\PHPLoginManagement\Service;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\User;
use App\PHPLoginManagement\Exception\ValidateException;
use App\PHPLoginManagement\Model\UserLoginRequest;
use App\PHPLoginManagement\Model\UserLoginResponse;
use App\PHPLoginManagement\Model\UserPasswordUpdateRequest;
use App\PHPLoginManagement\Model\UserPasswordUpdateResponse;
use App\PHPLoginManagement\Model\UserProfileUpdateRequest;
use App\PHPLoginManagement\Model\UserProfileUpdateResponse;
use App\PHPLoginManagement\Model\UserRegisterRequest;
use App\PHPLoginManagement\Model\UserRegisterResponse;
use App\PHPLoginManagement\Repository\UserRepository;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\MemoryUsageProcessor;

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
      $user->password = password_hash($request->password, PASSWORD_BCRYPT);

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

  public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
  {
    $this->validateUpdateRequest($request);

    try {
      Database::beginTransaction();
      $user = $this->userRepository->findById($request->id);
      $user->email = $request->email;
      $user->name = $request->name;
      $this->userRepository->update($user);
      Database::commitTransaction();
      $response = new UserProfileUpdateResponse;
      $response->user = $user;
      return $response;
    } catch (Exception $e) {
      Database::rollbackTransaction();
      throw $e;
    }
  }

  public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
  {
    $this->validateUpdatePasswordRequest($request);

    try {
      $user = $this->userRepository->findById($request->user_id);

      if ($user != null) {
        if (password_verify($request->oldPassword, $user->password)) {
          $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
          $this->userRepository->update($user);
          $response = new UserPasswordUpdateResponse;
          $response->user = $user;
          return $response;
        } else {
          throw new ValidateException(serialize(['oldPassword' => 'Old password is wrong']));
        }
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function login(UserLoginRequest $request): UserLoginResponse
  {
    $this->validateLoginRequest($request);
    $logger = new Logger(UserService::class);
    $logger->pushHandler(new StreamHandler(__DIR__ . '/../../app.log', Level::Info));
    try {

      $user = $this->userRepository->findByEmail($request->email);

      if ($user == null) {
        throw new ValidateException(serialize(["error_login" => "Incorrect email or password"]));
      }

      if (password_verify($request->password, $user->password)) {

        $logger->pushProcessor(new MemoryUsageProcessor());
        $logger->pushProcessor(function ($record) {
          $record['extra'] = [
            'dev' => 'nkolis',
          ];
          return $record;
        });
        $logger->info('Login success', ['user' => [
          'email' => $user->email,
          'name' => $user->name
        ]]);

        $response = new UserLoginResponse;
        $response->user = $user;
        return $response;
      } else {
        throw new ValidateException(serialize(["error_login" => "Incorrect email or password"]));
      }
    } catch (Exception $e) {

      $logger->pushProcessor(new MemoryUsageProcessor());
      $logger->pushProcessor(function ($record) {
        $record['extra'] = [
          'dev' => 'nkolis',
        ];
        return $record;
      });
      $logger->warning($e->getMessage(), ['user' => [
        'email' => $request->email,
        'password' => $request->password
      ]]);
      throw $e;
    }
  }

  private function validateRegisterRequest(UserRegisterRequest $request): void
  {
    $errors = [];

    foreach ($request as $key => $value) {

      // validasi jika request kosong
      $value = trim($value);
      if ($value == null || $value = '') {
        $errors[$key] = ucwords($key) . " can't be empty";
      }
    }

    // validasi jika email tidak valid
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $request->email)) {
      $errors['email'] = 'Invalid email';
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

  private function validateLoginRequest(UserLoginRequest $request): void
  {
    $errors = [];

    foreach ($request as $key => $value) {
      // validasi jika request kosong
      $value = trim($value);
      if ($value == null || $value = '') {
        $errors[$key] = ucwords($key) . " can't be empty";
      }
    }

    // validasi jika email tidak valid
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $request->email)) {
      $errors['email'] = 'Invalid email';
    }

    // tangkap error exception
    if (!empty($errors)) {
      throw new ValidateException(serialize($errors));
    }
  }

  private function validateUpdateRequest(UserProfileUpdateRequest $request): void
  {
    $errors = [];
    foreach ($request as $key => $value) {
      // validasi jika request kosong
      $value = trim($value);
      if ($value == null || $value = '') {
        $errors[$key] = ucwords($key) . " can't be empty";
      }
    }

    // validasi jika email tidak valid
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $request->email)) {
      $errors['email'] = 'Invalid email';
    }


    // validasi jika email sudah terdaftar
    $user_email = $this->userRepository->findByEmail($request->email);
    $user = $this->userRepository->findById($request->id);
    if ($user_email != null && $user->email != $user_email->email) {
      $errors['email'] = 'Email already registered';
    }

    // tangkap error exception
    if (!empty($errors)) {
      throw new ValidateException(serialize($errors));
    }
  }

  private function validateUpdatePasswordRequest(UserPasswordUpdateRequest $request): void
  {
    $errors = [];

    if (trim($request->oldPassword) == '' || $request->oldPassword == null) {
      $errors['oldPassword'] = "Old password can't be empty";
    }

    if (trim($request->newPassword) == '' || $request->newPassword == null) {
      $errors['newPassword'] = "New password can't be empty";
    }

    // tangkap error exception
    if (!empty($errors)) {
      throw new ValidateException(serialize($errors));
    }
  }
}
