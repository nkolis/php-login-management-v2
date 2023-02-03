<?php

namespace App\PHPLoginManagement\Model;

use App\PHPLoginManagement\Entity\User;

class UserSessionResponse
{
  public string $cookie;
  public string $user_id;
}
