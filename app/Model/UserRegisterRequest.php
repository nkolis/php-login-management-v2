<?php

namespace App\PHPLoginManagement\Model;

class UserRegisterRequest
{
  public ?string $id = null;
  public ?string $email = null;
  public ?string $name = null;
  public ?string $password = null;
}
