<?php

namespace Alura\Mvc\Controller;

use Alura\Mvc\Helper\HtmlRendererTrait;
use Alura\Mvc\Repository\VideoRepository;

class VideoListController implements Controller
{
  use HtmlRendererTrait;
  public function __construct(private VideoRepository $videoRepository)
  {
  }

  public function processRequest(): void
  {
    $videoList = $this->videoRepository->all();
    echo $this->renderTemplate(
      'video-list',
      ['videoList' => $videoList]
    );
  }
}
