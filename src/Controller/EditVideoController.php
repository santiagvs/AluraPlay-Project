<?php

namespace Alura\Mvc\Controller;

use Alura\Mvc\Entity\Video;
use Alura\Mvc\Helper\FlashMessageTrait;
use Alura\Mvc\Repository\VideoRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EditVideoController implements RequestHandlerInterface
{
  use FlashMessageTrait;
  private VideoRepository $videoRepository;
  public function __construct(VideoRepository $videorepository)
  {
    $this->videoRepository = $videorepository;
  }

  public function handle(ServerRequestInterface $request): ResponseInterface
  {
    $queryParams = $request->getQueryParams();
    $id = filter_var($queryParams['id'], FILTER_VALIDATE_INT);
    if (is_null($id) || $id === false) {
      $this->addErrorMessage('ID inválido!');
      return new Response(302, [
        'Location' => '/',
      ]);
    }

    $requestBody = $request->getParsedBody();
    $url = filter_var($requestBody['url'], FILTER_VALIDATE_URL);
    if ($url === false) {
      $this->addErrorMessage('URL inválida!');
      return new Response(400, [
        'Location' => "/editar-video?id=$id",
      ]);
    }

    $title = filter_var($requestBody['titulo']);
    if ($title === false) {
      $this->addErrorMessage('É necessário digitar um título.');
      return new Response(400, [
        'Location' => "/editar-video?id=$id",
      ]);
    }

    $video = new Video($url, $title);
    $files = $request->getUploadedFiles();
    /**
     * @var UploadedFileInterface $uploadedImage */

    $uploadedImage = $files['image'];

    if ($uploadedImage->getError() === UPLOAD_ERR_OK) {
      $finfo = new \finfo(FILEINFO_MIME_TYPE);
      $tmpFile = $uploadedImage->getStream()->getMetadata('uri');
      $mimetype = $finfo->file($tmpFile);

      if (str_starts_with($mimetype, 'image/')) {
        $safeFileName = uniqid('upload_') . '_' . pathinfo($uploadedImage->getClientFilename(), PATHINFO_BASENAME);
        $uploadedImage->moveTo(__DIR__ . '/../../public/img/uploads') . $safeFileName;
        $video->setFilePath($safeFileName);
      }
    }

    $success = $this->videoRepository->update($video);

    if ($success === false) {
      $this->addErrorMessage('Erro ao cadastrar edição das informações');
      return new Response(302, [
        'Location' => '/?sucesso=0',
      ]);
    } else {
      return new Response(200, [
        'Location' => '/?sucesso=1',
      ]);
    }
  }
}
