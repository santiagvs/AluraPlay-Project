<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Alura\Mvc\Repository\VideoRepository;
use Alura\Mvc\Controller\{
  Controller,
  VideoListController,
  VideoFormController,
  NewVideoController,
  EditVideoController,
  DeleteVideoController,
  Error404Controller
};

try {
    $pdo = new PDO('mysql:host=localhost;dbname=aluraplay', 'santiago', 's1Lv@83He');
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

$controller->processRequest();
