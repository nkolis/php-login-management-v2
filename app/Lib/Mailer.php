<?php

namespace App\PHPLoginManagement\Lib;

use App\PHPLoginManagement\Config\PHPMailerServer;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
  private static ?PHPMailer $mail = null;

  public static function get()
  {
    try {

      if (self::$mail == null) {
        self::$mail = new PHPMailer(true);
        $server_user_conf = PHPMailerServer::get();
        self::$mail->SMTPDebug = SMTP::DEBUG_OFF;                 //Enable verbose debug output
        self::$mail->isSMTP();                                    //Send using SMTP
        self::$mail->Host       = $server_user_conf['host'];      //Set the SMTP server to send through
        self::$mail->SMTPAuth   = true;
        self::$mail->SMTPKeepAlive = true;

        self::$mail->Username   = $server_user_conf['username'];   //SMTP username
        self::$mail->Password   = $server_user_conf['password'];   //SMTP password
        self::$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;     //Enable implicit TLS encryption
        self::$mail->Port       = 465;                             //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        self::$mail->setFrom($server_user_conf['username'], 'PHP Login Management');
      }
      return self::$mail;
    } catch (Exception $e) {
      throw $e;
    }
  }
}
