<?php

namespace Alura\Mvc\Controller;
use Alura\Mvc\Helper\HtmlRendererTrait;

class LoginFormController implements Controller
{
  use HtmlRendererTrait;
  public function processRequest(): void
  {
    if(array_key_exists('logado', $_SESSION) && $_SESSION['logado'] === true) {
      header('Location: /');
    }
    echo $this->renderTemplate('login-form');
  }
}
