<?php

namespace App\PHPLoginManagement\Entity;


class User
{
  public ?string $id = null;
  public ?string $email = null;
  public ?string $name = null;
  public ?string $verification_status = 'unverified';
  public ?string $password = null;
}
