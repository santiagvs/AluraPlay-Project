<?php
declare(strict_types=1);
namespace Alura\Mvc\Controller;

use Alura\Mvc\Helper\FlashMessageTrait;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PDO;
use Psr\Http\Server\RequestHandlerInterface;

class LoginController implements RequestHandlerInterface
{
    use FlashMessageTrait;
    private PDO $pdo;
    public function __construct()
    {
    $config = include __DIR__ . '/../../config.php';
    try {

      $this->pdo = new PDO("mysql:host={$config['dbhost']};dbname={$config['dbname']}", "{$config['dbuser']}", "{$config['dbpass']}");
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');

        $sql = 'SELECT * FROM users WHERE email = ?';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $email);
        $statement->execute();

        $userData = $statement->fetch(PDO::FETCH_ASSOC);
        $correctPassword = password_verify($password, $userData['password'] ?? '');

        if (!$correctPassword) {
            $this->addErrorMessage('Usuário ou senha inválidos');
            return new Response(302, ['Location' => '/login']);
        }

        if (password_needs_rehash($userData['password'], PASSWORD_ARGON2ID)) {
            $statement = $this->pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $statement->bindValue(1, password_hash($password, PASSWORD_ARGON2ID));
            $statement->bindValue(2, $userData['id']);
            $statement->execute();
        }

        $_SESSION['logado'] = true;
        return new Response(302, ['Location' => '/']);
    }
}
