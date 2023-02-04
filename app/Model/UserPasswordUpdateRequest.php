<?php

namespace App\PHPLoginManagement\Model;

class UserPasswordUpdateRequest
{
  public ?string $user_id;
  public ?string $oldPassword;
  public ?string $newPassword;
}
