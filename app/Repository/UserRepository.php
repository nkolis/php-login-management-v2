<?php

namespace App\PHPLoginManagement\Repository;

use App\PHPLoginManagement\Entity\User;
use Exception;
use PDO;


class UserRepository
{
  private PDO $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }

  public function save(User $user): User
  {
    try {
      $statement = $this->connection->prepare("INSERT INTO users(user_id, email, name, password)VALUES(?,?,?,?)");
      $statement->execute([$user->id, $user->email, $user->name, $user->password]);
      return $user;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function findById(string $id): ?User
  {
    try {
      $statement = $this->connection->prepare("SELECT user_id, email, name, password FROM users WHERE email = ?");
      $statement->execute([$id]);

      if ($row = $statement->fetch()) {
        $user = new User;
        $user->id = $row['user_id'];
        $user->email = $row['email'];
        $user->name = $row['name'];
        $user->password = $row['password'];
        return $user;
      } else {
        return null;
      }
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function deleteAll(): void
  {
    $this->connection->exec("DELETE FROM users");
  }
}
