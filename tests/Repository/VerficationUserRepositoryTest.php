<?php

namespace App\PHPLoginManagement\Repository;

use App\PHPLoginManagement\Config\Database;
use PHPUnit\Framework\TestCase;

class VerificationUserRepositoryTest extends TestCase
{
  protected $verificationRepository;

  public function __construct()
  {
    $this->verificationRepository = new VerificationUserRepository(Database::getConnection());
  }
}
