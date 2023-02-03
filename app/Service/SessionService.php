<?php

namespace App\PHPLoginManagement\Service;

use App\PHPLoginManagement\Entity\Session;
use App\PHPLoginManagement\Model\UserSessionRequest;
use App\PHPLoginManagement\Model\UserSessionResponse;
use App\PHPLoginManagement\Repository\SessionRepository;
use App\PHPLoginManagement\Repository\UserRepository;
use Exception;

class SessionService
{
  private UserRepository $userRepository;
  private SessionRepository $sessionRepository;
  public static $COOKIE = "PLM-SESSION";

  public function __construct($userRepository, $sessionRepository)
  {
    $this->userRepository = $userRepository;
    $this->sessionRepository = $sessionRepository;
  }

  public function create(UserSessionRequest $request): ?UserSessionResponse
  {

    try {
      $user = $this->userRepository->findById($request->user_id);

      if ($user != null && empty($_COOKIE[self::$COOKIE])) {
        setcookie(self::$COOKIE, $user->id, time() + 60 * 60 * 24 * 30, '/');

        $session = new Session();
        $session->id = $request->id;
        $session->user_id = $request->user_id;


        $this->sessionRepository->save($session);

        $response = new UserSessionResponse;
        $response->cookie = self::$COOKIE;
        $response->user_id = $user->id;
        return $response;
      } else {
        throw new Exception('Failed create session user not found');
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function currentSession(): ?UserSessionResponse
  {
    $user_id = $_COOKIE[self::$COOKIE] ?? null;

    $user = $this->userRepository->findById($user_id);
    if ($user != null) {
      $response = new UserSessionResponse;
      $response->cookie = self::$COOKIE;
      $response->user_id = $_COOKIE[self::$COOKIE];
      return $response;
    } else {
      return null;
    }
  }

  public function destroySession(): void
  {

    if (!empty($_COOKIE[self::$COOKIE])) {
      setcookie(self::$COOKIE, '', 1);
    }
  }
}
