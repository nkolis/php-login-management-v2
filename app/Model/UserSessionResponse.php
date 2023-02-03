<?php

namespace App\PHPLoginManagement\Model;

use App\PHPLoginManagement\Entity\User;

class UserSessionResponse
{
  public string $id;
  public string $user_id;
  public string $name;
  public string $email;
}
