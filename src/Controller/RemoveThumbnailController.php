<?php

namespace Alura\Mvc\Controller;

use Alura\Mvc\Repository\VideoRepository;

class RemoveThumbnailController implements Controller
{
  public function __construct(private VideoRepository $videoRepository)
  {
  }

  public function processRequest(): void
  {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (is_null($id) || $id === false) {
      header('Location: /?sucesso=0');
      return;
    }

    $success = $this->videoRepository->removeImage($id);
    if ($success === false) {
      header('Location: /?sucesso=0');
    } else {
      header('Location: /?sucesso=1');
    }
  }
}
