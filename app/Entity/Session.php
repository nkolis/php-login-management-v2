<?php

namespace App\PHPLoginManagement\Entity;

class Session
{
  public ?string $id = null;
  public ?string $user_id = null;
  public ?string $user_agent = null;
  public ?string $ip_addr = null;
  public ?string $expires = null;
}
