<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContentBuilderController extends AbstractController
{
    #[Route('/contentbuilder/assets/minimalist-blocks/content.js', name: 'content_builder_web_blocks')]
    public function webBlocks()
    {
        return $this->serveBlocks('contentbuilder/web/list.js.twig');
    }

    #[Route('/contentbuilder/assets/email-blocks/content.js', name: 'content_builder_email_blocks')]
    public function emailBlocks()
    {
        return $this->serveBlocks('contentbuilder/email/list.js.twig');
    }

    private function serveBlocks(string $template): Response
    {
        $response = $this->render($template);
        $response->headers->set('Content-Type', 'application/javascript');
        $response->setCache(['public' => true, 'max_age' => 3600, 's_maxage' => 3600]);

        return $response;
    }
}
