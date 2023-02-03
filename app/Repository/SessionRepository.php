<?php

namespace App\PHPLoginManagement\Repository;

use App\PHPLoginManagement\Entity\Session;
use Exception;
use PDO;

class SessionRepository
{

  private PDO $connection;

  function __construct($connection)
  {
    $this->connection = $connection;
  }


  public function save(Session $session): Session
  {
    try {
      $statement = $this->connection->prepare("INSERT INTO sessions (session_id, user_id)VALUES(?,?)");
      $statement->execute([$session->id, $session->user_id]);
      return $session;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function findById(?string $id): ?Session
  { {
      try {
        $statement = $this->connection->prepare("SELECT session_id,user_id FROM sessions WHERE session_id = ?");
        $statement->execute([$id]);

        if ($row = $statement->fetch()) {
          $session = new Session;
          $session->id = $row['session_id'];
          $session->user_id = $row['user_id'];

          return $session;
        } else {
          return null;
        }
      } catch (Exception $e) {
        throw $e;
      } finally {
        $statement->closeCursor();
      }
    }
  }

  public function deleteAll()
  {
    $this->connection->exec("DELETE FROM sessions");
  }
}
