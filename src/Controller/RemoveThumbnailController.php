<?php

namespace Alura\Mvc\Controller;

use Alura\Mvc\Helper\FlashMessageTrait;
use Alura\Mvc\Repository\VideoRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RemoveThumbnailController implements RequestHandlerInterface
{
  use FlashMessageTrait;
  public function __construct(private VideoRepository $videoRepository)
  {
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

    $success = $this->videoRepository->removeImage($id);
    if ($success === false) {
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
