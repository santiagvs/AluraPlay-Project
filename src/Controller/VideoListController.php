<?php

namespace Alura\Mvc\Controller;

use Alura\Mvc\Repository\VideoRepository;

class VideoListController extends HtmlController implements Controller
{
  public function __construct(private VideoRepository $videoRepository)
  {
  }

  public function processRequest(): void
  {
    $videoList = $this->videoRepository->all();
    $this->renderTemplate(
      'video-list',
      ['videoList' => $videoList]
    );
  }
}
