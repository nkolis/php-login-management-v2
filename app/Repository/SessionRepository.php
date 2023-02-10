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
      $statement = $this->connection->prepare("INSERT INTO sessions (session_id, user_id, user_agent, ip_addr, expires)VALUES(?,?,?,?,?)");
      $statement->execute([$session->id, $session->user_id, $session->user_agent, $session->ip_addr, $session->expires]);
      return $session;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function findById(?string $id): ?Session
  { {
      try {
        $statement = $this->connection->prepare("SELECT session_id, user_id, user_agent, ip_addr, expires FROM sessions WHERE session_id = ?");
        $statement->execute([$id]);

        if ($row = $statement->fetch()) {
          $session = new Session;
          $session->id = $row['session_id'];
          $session->user_id = $row['user_id'];
          $session->user_agent = $row['user_agent'];
          $session->ip_addr = $row['ip_addr'];
          $session->expires = $row['expires'];

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

  public function findByUserId(?string $id): ?Session
  {
    try {
      $statement = $this->connection->prepare("SELECT session_id,user_id,user_agent, ip_addr, expires FROM sessions WHERE user_id = ?");
      $statement->execute([$id]);

      if ($row = $statement->fetch()) {
        $session = new Session;
        $session->id = $row['session_id'];
        $session->user_id = $row['user_id'];
        $session->user_agent = $row['user_agent'];
        $session->ip_addr = $row['ip_addr'];
        $session->expires = $row['expires'];


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

  public function deleteByid(string $id)
  {
    $statement = $this->connection->prepare("DELETE FROM sessions WHERE session_id = ?");
    $statement->execute([$id]);
  }

  public function deleteExpireSessionByUserId(string $datenow, ?string $user_id)
  {
    $statement = $this->connection->prepare("DELETE FROM sessions WHERE user_id = ? AND expires < ?");
    $statement->execute([$user_id, $datenow]);
  }

  public function deleteAll()
  {
    $this->connection->exec("DELETE FROM sessions");
  }
}
