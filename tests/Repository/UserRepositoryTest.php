<?php

namespace App\PHPLoginManagement\Repository;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\User;
use PHPUnit\Framework\TestCase;

use Ramsey\Uuid\Uuid;


class UserRepositoryTest extends TestCase
{
  private UserRepository $userRepository;

  public function setUp(): void
  {
    $this->userRepository = new UserRepository(Database::getConnection());
    $this->userRepository->deleteAll();
  }

  public function testSaveSuccess()
  {
    $user = new User;
    $uuid = Uuid::uuid4();
    $user->id = $uuid->toString();
    $user->email = 'kholis@gmail.com';
    $user->name = 'Kholis';
    $user->password = '123';

    $this->userRepository->save($user);

    $result = $this->userRepository->findById($user->id);

    $this->assertEquals($user->id, $result->id);
    $this->assertEquals($user->email, $result->email);
    $this->assertEquals($user->name, $result->name);
    $this->assertEquals($user->password, $result->password);
  }

  public function testSaveError()
  {
    $this->expectException(\Exception::class);
    $user = new User;
    $uuid = Uuid::uuid4();
    $user->id = $uuid->toString();
    $user->email = 'kholis@gmail.com';
    $user->name = 'Kholis';
    $user->password = '123';

    $this->userRepository->save($user);
    $this->userRepository->save($user);
  }

  public function testFindByIdNotfound()
  {
    $user = $this->userRepository->findById('notfound');
    $this->assertNull($user);
  }

  public function findByEmail()
  {

    $user = new User;
    $uuid = Uuid::uuid4();
    $user->id = $uuid->toString();
    $user->email = 'kholis@gmail.com';
    $user->name = 'Kholis';
    $user->password = '123';

    $this->userRepository->save($user);
    $result = $this->userRepository->findByEmail($user->email);
    $this->assertEquals($user->id, $result->id);
    $this->assertEquals($user->email, $result->email);
    $this->assertEquals($user->name, $result->name);
    $this->assertEquals($user->password, $result->password);
  }

  public function testFindByEmailNotfound()
  {
    $user = $this->userRepository->findByEmail('notfound');
    $this->assertNull($user);
  }
}
