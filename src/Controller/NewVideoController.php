<?php

namespace Alura\Mvc\Controller;

use Alura\Mvc\Entity\Video;
use Alura\Mvc\Repository\VideoRepository;

class NewVideoController implements Controller
{
  private VideoRepository $videoRepository;
  public function __construct(VideoRepository $videorepository)
  {
    $this->videoRepository = $videorepository;
  }

  public function processRequest(): void
  {
    $url = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);
    if ($url === false) {
      header('Location: /?sucesso=0');
      exit();
    }

    $title = filter_input(INPUT_POST, 'titulo');
    if ($title === false) {
      header('Location: /?sucesso=0');
      exit();
    }

    $video = new Video($url, $title);
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
      move_uploaded_file(
        $_FILES['image']['tmp_name'],
        __DIR__ . '/../../public/img/uploads/' . $_FILES['image']['name']
      );
      $video->setFilePath($_FILES['image']['name']);
    }

    $success = $this->videoRepository->add(new Video($url, $title));

    if ($success === false) {
      header('Location: /?sucesso=0');
    } else {
      header('Location: /?sucesso=1');
    }
  }
}
