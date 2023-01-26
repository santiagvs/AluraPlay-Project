<?php

namespace Alura\Mvc\Controller;

class LoginFormController extends HtmlController implements Controller
{
  public function processRequest(): void
  {
    if(array_key_exists('logado', $_SESSION) && $_SESSION['logado'] === true) {
      header('Location: /');
    }
    $this->renderTemplate('login-form');
  }
}
