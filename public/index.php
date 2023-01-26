<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Alura\Mvc\Repository\VideoRepository;
use Alura\Mvc\Controller\Error404Controller;

$config = require_once __DIR__ . '/../config.php';

try {
    $pdo = new PDO("mysql:host={$config['dbhost']};dbname={$config['dbname']}", "{$config['dbuser']}", "{$config['dbpass']}");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$videoRepository = new VideoRepository($pdo);

$routes = require_once __DIR__ . '/../config/routes.php';
$pathInfo = $_SERVER['PATH_INFO'] ?? '/';
$httpMethod = $_SERVER['REQUEST_METHOD'];

session_start();
session_regenerate_id();
$isLoginRoute = $pathInfo === '/login';
if (!array_key_exists('logado', $_SESSION) && !$isLoginRoute) {
  header('Location: /login');
  return;
}

$key = "$httpMethod|$pathInfo";

if (array_key_exists($key, $routes)) {
    $controllerClass = $routes[$key];

    $controller = new $controllerClass($videoRepository);
} else {
    $controller = new Error404Controller();
}

$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

$creator = new \Nyholm\Psr7Server\ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);

$request = $creator->fromGlobals();

$response = $controller->handle($request);

http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header (sprintf('%s: %s', $name, $value), false);
    }
}

echo $response->getBody();
