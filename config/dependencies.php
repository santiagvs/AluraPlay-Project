<?php

use DI\ContainerBuilder;

$builder = new ContainerBuilder;
$builder->addDefinitions([
  PDO::class => function (): PDO {
    $config = include __DIR__ . '/../config.php';
    $pdo = new PDO("mysql:host={$config['dbhost']};dbname={$config['dbname']}", "{$config['dbuser']}", "{$config['dbpass']}");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
    }
]);

$container = $builder->build();

return $container;
