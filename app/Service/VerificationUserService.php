<?php

namespace App\PHPLoginManagement\Service;

use App\PHPLoginManagement\Entity\VerificationUser;
use App\PHPLoginManagement\Model\UserVerificationRequest;
use App\PHPLoginManagement\Model\UserVerificationResponse;
use App\PHPLoginManagement\Repository\VerificationUserRepository;
use DateTime;
use Exception;

class VerificationUserService
{
  private VerificationUserRepository $verificationUserRepository;
  private static $expire_code = 60 * 10;

  public function __construct($verificationUserRepository)
  {
    $this->verificationUserRepository = $verificationUserRepository;
  }


  public function generateCodeVerification(UserVerificationRequest $request): UserVerificationResponse
  {
    try {
      $request->code = $this->randCode(6);
      $user_verification = $this->verificationUserRepository->findByUserId($request->user_id);
      if ($user_verification != null) {
        $user_verification->code = $request->code;
        $user_verification->updated_at = date('Y-m-d H:i:s', time());
        $verification = $this->verificationUserRepository->update($user_verification);
      } else {
        $verification = new VerificationUser;
        $verification->user_id = $request->user_id;
        $verification->code = $request->code;
        $verification = $this->verificationUserRepository->save($verification);
      }
      $response = new UserVerificationResponse;
      $response->verification = $verification;
      return $response;
    } catch (\Exception $e) {
      throw $e;
    }
  }


  public function currentCodeVerification(string $user_id): VerificationUser
  {
    try {
      $datenow = time();
      $codeExpire = false;
      $verification_user = $this->verificationUserRepository->findByUserId($user_id);

      if ($verification_user == null) {
        throw new \Exception("Klik send code and check your mail box!");
      }

      $created_at = new DateTime($verification_user->created_at);
      $expire = $created_at->getTimestamp() + self::$expire_code;


      if ($datenow > $expire) {
        $codeExpire = true;
      }

      if ($verification_user->updated_at !== null) {
        $updated_at = new DateTime($verification_user->updated_at);
        $expire = $updated_at->getTimestamp() + self::$expire_code;
        if ($datenow > $expire) {
          $codeExpire = true;
        }
      }

      if ($codeExpire) {
        throw new \Exception("Your code verification is expired, send code again!");
      }

      return $verification_user;
    } catch (\Exception $e) {
      throw $e;
    }
  }



  private function randCode(int $length): array|string
  {
    $code = [];

    while (sizeof($code) < $length) {
      $code[] = rand(0, 9);
    }

    return implode($code);
  }
}
