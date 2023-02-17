<?php

namespace App\PHPLoginManagement\Middleware;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Core\View;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Repository\VerificationUserRepository;
use App\PHPLoginManagement\Service\SessionService;
use App\PHPLoginManagement\Service\VerificationUserService;
use Exception;

class MustVerifyPasswordMiddleware implements Middleware
{
  private SessionService $sessionService;
  private VerificationUserService $verificationService;

  function __construct()
  {
    $connection = Database::getConnection();
    $userRepository = new UserRepository($connection);
    $sessionRepository = new SessionRepository($connection);
    $verificationRepository = new VerificationUserRepository($connection);
    $this->sessionService = new SessionService($userRepository, $sessionRepository);
    $this->verificationService = new VerificationUserService($verificationRepository, $userRepository);
  }

  public function before(): void
  {
    $url = $_SERVER['REQUEST_URI'];
    try {

      $current = $this->sessionService->currentSession("PLM-RESET-PASSWORD");
      if ($current == null) {
        View::redirect('/users/password_reset');
      }
      $verification_user = $this->verificationService->currentCodeVerification($current->user_id ?? null);
      if ($verification_user != null && $url == '/php-login-management-v2/public/users/password_reset/change') {
        View::redirect('/users/password_reset/verify');
      }
    } catch (Exception) {
      if ($url == '/php-login-management-v2/public/users/password_reset/verify') {
        View::redirect('/users/password_reset/change');
      }
    }
  }
}
