<?php

namespace App\PHPLoginManagement\Repository;

use App\PHPLoginManagement\Config\Database;
use App\PHPLoginManagement\Entity\Session;
use App\PHPLoginManagement\Entity\User;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;

class SessionRepositoryTest extends TestCase
{
  private SessionRepository $sessionRepository;
  private UserRepository $userRepository;

  function setUp(): void
  {
    $connection = Database::getConnection();

    $this->sessionRepository = new SessionRepository($connection);
    $this->userRepository = new UserRepository($connection);

    $this->sessionRepository->deleteAll();
    $this->userRepository->deleteAll();
  }

  function testSaveSuccess()
  {
    $user = new User;
    $uuid = Uuid::uuid4();
    $user->id = $uuid->toString();
    $user->email = 'eren@gmail.com';
    $user->name = 'Eren Yaiger';
    $user->password = password_hash('eren123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $session = new Session;
    $uuid = Uuid::uuid4();
    $session->id = $uuid->toString();
    $session->user_id = $user->id;
    $session->user_agent = 'Mozilla';
    $session->ip_addr = getenv("REMOTE_ADDR");


    $this->sessionRepository->save($session);

    $result = $this->sessionRepository->findById($session->id);

    $this->assertEquals($session->id, $result->id);
    $this->assertEquals($session->user_id, $result->user_id);
  }

  function testSaveDuplicateError()
  {
    $this->expectException(Exception::class);
    $user = new User;
    $uuid = Uuid::uuid4();
    $user->id = $uuid->toString();
    $user->email = 'eren@gmail.com';
    $user->name = 'Eren Yaiger';
    $user->password = password_hash('eren123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $session = new Session;
    $uuid = Uuid::uuid4();
    $session->id = $uuid->toString();
    $session->user_id = $user->id;

    $this->sessionRepository->save($session);
    $this->sessionRepository->save($session);
  }

  function testFindByIdNotFound()
  {
    $result = $this->sessionRepository->findById('notfound');
    $this->assertNull($result);;
  }

  function testFindByUserId()
  {
    $user = new User;
    $uuid = Uuid::uuid4();
    $user->id = $uuid->toString();
    $user->email = 'eren@gmail.com';
    $user->name = 'Eren Yaiger';
    $user->password = password_hash('eren123', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $session = new Session;
    $uuid = Uuid::uuid4();
    $session->id = $uuid->toString();
    $session->user_id = $user->id;
    $session->user_agent = 'Mozilla';
    $session->ip_addr = getenv("REMOTE_ADDR");
    $this->sessionRepository->save($session);

    $result = $this->sessionRepository->findByUserId($user->id);

    $this->assertEquals(Session::class, $result::class);
    $this->assertEquals($session->id, $result->id);
    $this->assertEquals($session->user_id, $result->user_id);
    $this->assertEquals($session->user_agent, $result->user_agent);
    $this->assertEquals($session->ip_addr, $result->ip_addr);
  }

  // function testFindByUserIdResultsMoreThanOne()
  // {
  //   $user = new User;
  //   $uuid = Uuid::uuid4();
  //   $user->id = $uuid->toString();
  //   $user->email = 'eren@gmail.com';
  //   $user->name = 'Eren Yaiger';
  //   $user->password = password_hash('eren123', PASSWORD_BCRYPT);
  //   $this->userRepository->save($user);

  //   $session = new Session;
  //   $uuid = Uuid::uuid4();
  //   $session->id = $uuid->toString();
  //   $session->user_id = $user->id;
  //   $session->user_agent = 'Mozilla';
  //   $session->ip_addr = getenv("REMOTE_ADDR");
  //   $this->sessionRepository->save($session);
  //   $uuid = Uuid::uuid4();
  //   $session->id = $uuid->toString();
  //   $session->user_id = $user->id;
  //   $session->user_agent = 'Chrome';
  //   $session->ip_addr = getenv("REMOTE_ADDR");
  //   $this->sessionRepository->save($session);

  //   $result = $this->sessionRepository->findByUserId($user->id);
  //   var_dump($result);
  // }
}
