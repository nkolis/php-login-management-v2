<?php

namespace App\PHPLoginManagement\Middleware;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Core\View;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Service\SessionService;


class MustNotVerifyPasswordMiddleware implements Middleware
{
  private SessionService $sessionService;


  function __construct()
  {
    $connection = Database::getConnection();
    $userRepository = new UserRepository($connection);
    $sessionRepository = new SessionRepository($connection);
    $this->sessionService = new SessionService($userRepository, $sessionRepository);
  }

  public function before(): void
  {
    $current = $this->sessionService->currentSession("PLM-RESET-PASSWORD");
    if ($current != null) {
      View::redirect('/users/password_reset/verify');
    }
  }
}
