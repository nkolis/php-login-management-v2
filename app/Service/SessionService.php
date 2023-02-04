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
        setcookie(self::$COOKIE, $request->id, time() + 60 * 60 * 24 * 30, '/');

        $session = new Session();
        $session->id = $request->id;
        $session->user_id = $request->user_id;


        $this->sessionRepository->save($session);

        $response = new UserSessionResponse;
        $response->id = $session->id;
        $response->user_id = $user->id;
        $response->email = $user->email;
        $response->name = $user->name;
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
    $id = $_COOKIE[self::$COOKIE] ?? null;

    $session = $this->sessionRepository->findById($id);
    $user = $this->userRepository->findById($session->user_id ?? null);
    if ($user != null && $session != null) {
      $response = new UserSessionResponse;
      $response->id = $session->id;
      $response->user_id = $user->id;
      $response->email = $user->email;
      $response->name = $user->name;
      return $response;
    } else {
      setcookie(Self::$COOKIE, '', 1, '/');
      return null;
    }
  }

  public function destroySession(): void
  {

    $current = $this->currentSession();

    if ($current != null) {
      setcookie(self::$COOKIE, '', 1, '/');
      $session = $this->sessionRepository->findById($current->id);
      $this->sessionRepository->deleteByid($session->id);
    }
  }
}
