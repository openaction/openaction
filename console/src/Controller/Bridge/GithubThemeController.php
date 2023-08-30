<?php

namespace App\Controller\Bridge;

use App\Controller\AbstractController;
use App\Theme\GithubThemeEventHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/webhook/github/theme', stateless: true)]
class GithubThemeController extends AbstractController
{
    #[Route('/event/website', name: 'webhook_github_theme_event_website', methods: ['POST'], stateless: true)]
    public function event(GithubThemeEventHandler $handler, Request $request)
    {
        if (!$eventName = $request->headers->get('X-GitHub-Event')) {
            throw $this->createNotFoundException('No event provided');
        }

        if (!$signature = $request->headers->get('X-Hub-Signature-256')) {
            throw $this->createNotFoundException('No signature provided');
        }

        if (!$handler->handleWebsiteThemeEvent($eventName, $request->getContent(), $signature)) {
            throw $this->createNotFoundException('Handling event failed (unsupported event?)');
        }

        return new JsonResponse(['status' => 'handled']);
    }
}
