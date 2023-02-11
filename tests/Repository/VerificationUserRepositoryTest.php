<?php

namespace App\PHPLoginManagement\Repository;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\User;
use App\PHPLoginManagement\Entity\VerificationUser;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class VerificationUserRepositoryTest extends TestCase
{
  private $verificationRepository;
  private VerificationUser $verificationData;

  public function setUp(): void
  {
    $connection = Database::getConnection();
    $this->verificationRepository = new VerificationUserRepository($connection);
    $userRepository = new UserRepository($connection);
    $sessionRepository = new SessionRepository($connection);
    $user = new User;
    $uuid = Uuid::uuid4();
    $user->id = $uuid->toString();
    $user->email = 'nurkholis@gmail.com';
    $user->name = 'Kholis';
    $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
    $verification = new VerificationUser;
    $verification->user_id = $user->id;
    $verification->code = '123456';
    $this->verificationData = $verification;

    $sessionRepository->deleteAll();
    $this->verificationRepository->deleteAll();
    $userRepository->deleteAll();
    $userRepository->save($user);
  }

  public function testSaveSuccess()
  {
    $verification = $this->verificationRepository->save($this->verificationData);
    $result = $this->verificationRepository->findByUserId($verification->user_id);
    $this->assertEquals($verification->id, $result->id);
    $this->assertEquals($verification->code, $result->code);
    $this->assertEquals($verification->updated_at, $result->updated_at);
  }

  public function testSaveError()
  {
    $this->expectException(Exception::class);
    $this->verificationRepository->save($this->verificationData);
    $this->verificationRepository->save($this->verificationData);
  }

  public function testFindByIdNotfound()
  {
    $result = $this->verificationRepository->findByUserId('notfound');
    $this->assertNull($result);
  }


  public function testUpdateSuccess()
  {
    $verification = $this->verificationRepository->save($this->verificationData);
    $this->verificationData->code = '54321';
    $this->verificationData->updated_at = '2023-02-11';
    $this->verificationRepository->update($this->verificationData);
    $result = $this->verificationRepository->findByUserId($verification->user_id);

    $this->assertEquals($verification->id, $result->id);
    $this->assertEquals('54321', $result->code);
    $this->assertEquals('2023-02-11 00:00:00', $result->updated_at);
  }
}
