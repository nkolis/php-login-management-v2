<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

//Load Composer's autoloader
require __DIR__ . '/../../vendor/autoload.php';


$mail = new PHPMailer(true);


try {
  //Server settings
  $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
  $mail->isSMTP();                                            //Send using SMTP
  $mail->Host       = 'smtp.gmail.com';                   //Set the SMTP server to send through
  $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
  $mail->Username   = 'setiawhan76@gmail.com';                     //SMTP username
  $mail->Password   = 'uxkfqrkejklsuzxy';                               //SMTP password
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
  $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

  //Recipients
  $mail->setFrom('setiawhan76@gmail.com', 'PHP Login Management');
  $mail->addAddress('nurkholis010@gmail.com', 'Nur Kholis');     //Add a recipient
  //$mail->addAddress('ellen@example.com');               //Name is optional
  $mail->addReplyTo('setiawhan76@gmail.com', 'Information');
  // $mail->addCC('cc@example.com');
  // $mail->addBCC('bcc@example.com');



  //Content
  $mail->isHTML(true);                                  //Set email format to HTML
  $mail->Subject = 'PHP Login Management - Verification code';
  $mail->Body    = '<p style="font-size: 20px">Your verification code is <b>237873</b></p>';
  $mail->AltBody = 'Your verification code is 237873';

  // $mail->send();
  //echo 'Message has been sent';
} catch (Exception $e) {
  echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
