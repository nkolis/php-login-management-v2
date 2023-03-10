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
  public static array $CLIENT;

  public function __construct($userRepository, $sessionRepository)
  {
    $this->userRepository = $userRepository;
    $this->sessionRepository = $sessionRepository;
    self::$CLIENT = [
      'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? getenv("HTTP_USER_AGENT"),
      'ip_addr' => $_SERVER['REMOTE_ADDR'] ?? getenv("REMOTE_ADDR"),
    ];
  }

  public function create(UserSessionRequest $request, $session_name = "PLM-SESSION", $expire = DEFAULT_COOKIE_EXPIRES): ?UserSessionResponse
  {

    try {

      $user = $this->userRepository->findById($request->user_id);
      $session = $this->sessionRepository->findByUserId($request->user_id);

      if ($session != null && $session->user_agent == self::$CLIENT['user_agent'] && $session->ip_addr == self::$CLIENT['ip_addr']) {
        $this->sessionRepository->deleteByid($session->id);
      }

      if ($user != null && empty($_COOKIE[$session_name])) {
        $expire = time() + $expire;
        setcookie($session_name, $request->id, $expire, '/');
        $session = new Session();
        $session->id = $request->id;
        $session->user_id = $request->user_id;
        $session->user_agent = self::$CLIENT['user_agent'];
        $session->ip_addr = self::$CLIENT['ip_addr'];
        $session->expires = date('Y-m-d H:i:s', $expire);
        $this->sessionRepository->save($session);

        $response = new UserSessionResponse;
        $response->id = $session->id;
        $response->user_id = $user->id;
        $response->email = $user->email;
        $response->name = $user->name;
        $response->verification_status = $user->verification_status;
        return $response;
      } else {
        throw new Exception(serialize('Failed create session user not found'));
        return null;
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function currentSession($session_name = "PLM-SESSION"): ?UserSessionResponse
  {

    $session_id = $_COOKIE[$session_name] ?? null;
    $session = $this->sessionRepository->findById($session_id);
    $this->sessionRepository->deleteExpireSessionByUserId(date('Y-m-d H:i:s', time()), $session->user_id ?? null);
    $user = $this->userRepository->findById($session->user_id ?? null);
    if ($user != null && $session != null && $session->user_agent == self::$CLIENT['user_agent'] && $session->ip_addr == self::$CLIENT['ip_addr']) {
      $response = new UserSessionResponse;
      $response->id = $session->id;
      $response->user_id = $user->id;
      $response->email = $user->email;
      $response->name = $user->name;
      $response->verification_status = $user->verification_status;
      return $response;
    } else {
      setcookie($session_name, '', 1, '/');
      return null;
    }
  }

  public function destroySession($session_name = "PLM-SESSION"): void
  {
    $current = $this->currentSession($session_name);
    if ($current != null) {
      setcookie($session_name, '', 1, '/');
      $this->sessionRepository->deleteByid($current->id);
    }
  }
}
