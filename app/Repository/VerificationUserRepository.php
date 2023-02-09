<?php

namespace App\PHPLoginManagement\Repository;

use App\PHPLoginManagement\Entity\VerificationUser;
use Exception;
use PDO;

class VerificationUserRepository
{
  private PDO $connection;

  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  public function save(VerificationUser $verication): VerificationUser
  {
    try {
      $statement = $this->connection->prepare("INSERT INTO verification_users(user_id, code)VALUES(?,?)");
      $statement->execute([$verication->user_id, $verication->code]);
      $verication->id = $this->connection->lastInsertId();
      return $verication;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function findById(?string $id): ?VerificationUser
  {
    try {
      $statement = $this->connection->prepare("SELECT id, user_id, code, created_at, updated_at FROM verification_users WHERE id = ?");
      $statement->execute([$id]);

      if ($row = $statement->fetch()) {
        $verification = new VerificationUser;
        $verification->id = $row['id'];
        $verification->user_id = $row['user_id'];
        $verification->code = $row['code'];
        $verification->created_at = $row['created_at'];
        $verification->updated_at = $row['updated_at'];
        return $verification;
      } else {
        return null;
      }
    } catch (Exception $e) {
      throw $e;
    } finally {
      $statement->closeCursor();
    }
  }

  public function deleteAll(): void
  {
    $this->connection->exec("DELETE FROM verification_users");
  }
}
