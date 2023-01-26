<?php

namespace Alura\Mvc\Controller;
use PDO;

class LoginController implements Controller
{
  private PDO $pdo;

  public function __construct()
  {
    try {
      $this->pdo = new PDO('mysql:host=localhost;dbname=aluraplay', 'santiago', 's1Lv@83He');
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  }

  public function processRequest(): void
  {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password');
    $sql = 'SELECT * FROM users WHERE email = ?;';

    $statement = $this->pdo->prepare($sql);
    $statement->bindValue(1, $email);
    $statement->execute();

    $userData = $statement->fetch(PDO::FETCH_ASSOC);
    $correctPassword = password_verify($password, $userData['password'] ?? '');

    if (password_needs_rehash($userData['password'], PASSWORD_ARGON2ID)) {
      $statement = $this->pdo->prepare('UPDATE users SET password = ? WHERE id = ?;');
      $statement->bindValue(1, password_hash($password, PASSWORD_ARGON2ID));
      $statement->bindValue(2, $userData['id']);
      $statement->execute();
    }

    if ($correctPassword) {
      $_SESSION['logado'] = true;
      header('Location: /');
    } else {
      header('Location: /login?sucesso=0');
    }
  }
}
