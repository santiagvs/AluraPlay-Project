<?php

namespace Alura\Mvc\Controller;

use Alura\Mvc\Entity\Video;
use Alura\Mvc\Helper\FlashMessageTrait;
use Alura\Mvc\Repository\VideoRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NewVideoController implements RequestHandlerInterface
{
  use FlashMessageTrait;
  private VideoRepository $videoRepository;
  public function __construct(VideoRepository $videorepository)
  {
    $this->videoRepository = $videorepository;
  }

  public function handle(ServerRequestInterface $request): ResponseInterface
  {
    $requestBody = $request->getParsedBody();
    $url = filter_var($requestBody['url'], FILTER_VALIDATE_URL);
    if ($url === false) {
      $this->addErrorMessage('URL inválida!');
      return new Response(400, [
        'Location' => "/novo-video",
      ]);
    }

    $title = filter_var($requestBody['titulo']);
    if ($title === false) {
      $this->addErrorMessage('É necessário digitar um título.');
      return new Response(400, [
        'Location' => "/novo-video",
      ]);
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
      return new Response(302, [
        'Location' => '/?sucesso=0',
      ]);
    } else {
      return new Response(201, [
        'Location' => '/?sucesso=1',
      ]);
    }
  }
}
