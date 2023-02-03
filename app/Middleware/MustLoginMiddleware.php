<?php

namespace App\PHPLoginManagement\Middleware;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Core\View;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Service\SessionService;

class MustLoginMiddleware implements Middleware
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
    if ($this->sessionService->currentSession() == null) {
      View::redirect('/users/login');
    }
  }
}
