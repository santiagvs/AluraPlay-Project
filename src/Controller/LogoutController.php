<?php

namespace Alura\Mvc\Controller;

class LogoutController implements Controller
{
  public function processRequest(): void
  {
    session_destroy();
    header('Location: /login');
  }
}
