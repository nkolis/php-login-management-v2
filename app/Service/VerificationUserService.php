<?php

namespace App\PHPLoginManagement\Service;

use App\PHPLoginManagement\Config\PHPMailerServer;
use App\PHPLoginManagement\Entity\VerificationUser;
use App\PHPLoginManagement\Exception\ValidateException;
use App\PHPLoginManagement\Model\UserVerificationRequest;
use App\PHPLoginManagement\Model\UserVerificationResponse;
use App\PHPLoginManagement\Repository\UserRepository;
use App\PHPLoginManagement\Repository\VerificationUserRepository;
use DateTime;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


class VerificationUserService
{
  private VerificationUserRepository $verificationUserRepository;
  private UserRepository $userRepository;
  private static $expire_code = 60 * 10;

  public function __construct($verificationUserRepository, $userRepository)
  {
    $this->verificationUserRepository = $verificationUserRepository;
    $this->userRepository = $userRepository;
  }


  private function generateCodeVerification(UserVerificationRequest $request): UserVerificationResponse
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
        throw new \Exception(serialize(["verification" => "Klik send code and check your mail box!"]));
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
        } else {
          $codeExpire = false;
        }
      }

      if ($codeExpire) {
        throw new \Exception(serialize(["verification" => "Your code verification is expired, send code again!"]));
      }

      return $verification_user;
    } catch (\Exception $e) {
      throw $e;
    }
  }


  public function sendVerificationCode(UserVerificationRequest $request): void
  {
    try {
      $user = $this->userRepository->findById($request->user_id);

      if ($user != null) {
        $this->generateCodeVerification($request);
        $user_verification = $this->currentCodeVerification($user->id);

        // PHP Mailer
        $mail = new PHPMailer(true);
        //Server settings
        $server_user_conf = PHPMailerServer::get();
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $server_user_conf['host'];                   //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $server_user_conf['username'];                     //SMTP username
        $mail->Password   = $server_user_conf['password'];                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('setiawhan76@gmail.com', 'PHP Login Management');
        $mail->addAddress($user->email, $user->name);     //Add a recipient
        //$mail->addAddress('ellen@example.com');               //Name is optional
        $mail->addReplyTo('setiawhan76@gmail.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');



        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'PHP Login Management - Verification code';
        $mail->Body    = "<p style='font-size: 20px'>Your verification code is <b>{$user_verification->code}</b></p>";
        $mail->AltBody = "Your verification code is {$user_verification->code}";
        if (getenv("mode") != "test") {
          $mail->send();
        }
        echo 'Code has been sent';
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function verifyUser(UserVerificationRequest $request): void
  {
    $this->validateVerifyRequest($request);
    try {

      $user_verification = $this->currentCodeVerification($request->user_id);


      if ($user_verification->code == $request->code) {
        $user = $this->userRepository->findById($request->user_id);
        $user->verification_status = "verified";
        $this->userRepository->update($user);
        $this->verificationUserRepository->deleteByUserid($request->user_id);
      } else {
        throw new Exception(serialize(["verification" => "Incorrect code verification"]));
      }
    } catch (Exception $e) {
      throw $e;
    }
  }


  private function validateVerifyRequest(UserVerificationRequest $request)
  {
    if (trim($request->code) == '' || $request->code == null) {
      throw new ValidateException(serialize(["code" => "Code can't be empty"]));
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
