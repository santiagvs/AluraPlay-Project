<?php

namespace Alura\Mvc\Repository;
use PDO;

class UserRepository
{
  public function __construct(private PDO $pdo)
  {
    $config = include __DIR__ . '/../../config.php';
    try {
      $this->pdo = new PDO("mysql:host={$config['dbhost']};dbname={$config['dbname']}", "{$config['dbuser']}", "{$config['dbpass']}");

      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  }

  public function findUserData(string $email): array
  {
    $sql = 'SELECT * FROM users WHERE email = ?';
    $statement = $this->pdo->prepare($sql);
    $statement->bindValue(1, $email);
    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public function updatePassword(string $email, string $password): bool
  {
    $id = $this->findUserData($email)['id'];
    $statement = $this->pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
    $statement->bindValue(1, password_hash($password, PASSWORD_ARGON2ID));
    $statement->bindValue(2, $id);

    return $statement->execute();
  }
}
