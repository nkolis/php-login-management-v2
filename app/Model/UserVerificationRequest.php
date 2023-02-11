<?php

namespace App\PHPLoginManagement\Model;


class UserVerificationRequest
{
  public string $user_id;
  public string $code;
  public ?string $updated_at = null;
}
