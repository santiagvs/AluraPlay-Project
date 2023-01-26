<?php

namespace Alura\Mvc\Controller;

use Alura\Mvc\Entity\Video;
use Alura\Mvc\Helper\FlashMessageTrait;
use Alura\Mvc\Repository\VideoRepository;

class NewVideoController implements Controller
{
  use FlashMessageTrait;
  private VideoRepository $videoRepository;
  public function __construct(VideoRepository $videorepository)
  {
    $this->videoRepository = $videorepository;
  }

  public function processRequest(): void
  {
    $url = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);
    if ($url === false) {
      $this->addErrorMessage('URL inválida.');
      header('Location: /novo-video');
      return;
    }

    $title = filter_input(INPUT_POST, 'titulo');
    if ($title === false) {
      $this->addErrorMessage('Título não informado.');
      header('Location: /novo-video');
      return;
    }

    $video = new Video($url, $title);
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
      $finfo = new \finfo(FILEINFO_MIME_TYPE);
      $mimetype = $finfo->file($_FILES['image']['tmp_name']);

      if (str_starts_with($mimetype, 'image/')) {
        $safeFileName = uniqid('upload_') . '_' . pathinfo($_FILES['image']['name'], PATHINFO_BASENAME);
        move_uploaded_file(
          $_FILES['image']['tmp_name'],
          __DIR__ . '/../../public/img/uploads/' . $safeFileName
        );
        $video->setFilePath($safeFileName);
      }
    }

    $success = $this->videoRepository->add(new Video($url, $title));

    if ($success === false) {
      $this->addErrorMessage('Erro ao cadastrar vídeo.');
      header('Location: /novo-video');
    } else {
      header('Location: /?sucesso=1');
    }
  }
}
