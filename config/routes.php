<?php

use Alura\Mvc\Controller\{
  VideoListController,
  VideoFormController,
  NewVideoController,
  EditVideoController,
  DeleteVideoController,
  LoginFormController,
  LoginController,
  LogoutController,
  RemoveThumbnailController
};


return [
  'GET|/' => VideoListController::class,
  'GET|/novo-video' => VideoFormController::class,
  'POST|/novo-video' => NewVideoController::class,
  'GET|/editar-video' => VideoFormController::class,
  'POST|/editar-video' => EditVideoController::class,
  'GET|/remover-video' => DeleteVideoController::class,
  'GET|/login' => LoginFormController::class,
  'POST|/login' => LoginController::class,
  'GET|/logout' => LogoutController::class,
  'GET|/remover-capa' => RemoveThumbnailController::class
];
