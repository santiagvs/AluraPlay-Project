<?php

namespace Alura\Mvc\Repository;
use PDO;

class UserRepository
{
  public function __construct(private PDO $pdo)
  {
  }

  public function find($id)
  {
    $statement = $this->pdo->prepare("SELECT * FROM users WHERE id = ?;");
    $statement->bindValue(1, $id);
    $statement->execute();
  }

  public function all(): type
  {
    #type here...
  }
}
