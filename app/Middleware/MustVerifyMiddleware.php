<?php

namespace App\PHPLoginManagement\Middleware;

use App\PHPLoginManagement\Config\BaseURL;
use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Core\View;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Service\SessionService;


class MustVerifyMiddleware implements Middleware
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
    $current = $this->sessionService->currentSession();
    if ($current->verification_status == 'unverified') {
      View::render('User/dashboard', [
        'title' => 'User dashboard',
        'user' => [
          'name' => $current->name ?? '',
          'verification_status' => $current->verification_status ?? ''
        ],
        'swal' => json_encode([
          'icon' => 'warning',
          'title' => 'Please verify your account!',
          'showConfirmButton' => true,
          'redirect-url' => BaseURL::get() . '/users/dashboard'
        ])
      ]);
    }
  }
}
