<?php

namespace App\PHPLoginManagement\Middleware;

interface Middleware
{
  public function before(): void;
}
