<?php

namespace App\Controller\Bridge;

use App\Community\Webhook\WingsWebhookMessage;
use App\Controller\AbstractController;
use App\Entity\Project;
use App\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class WingsController extends AbstractController
{
    #[Route('/webhook/wings/{uuid}', name: 'webhook_wings', stateless: true)]
    public function index(MessageBusInterface $bus, Project $project, Request $request)
    {
        if (!$token = $request->query->get('t')) {
            throw $this->createNotFoundException();
        }

        if ($token !== $project->getApiToken()) {
            throw $this->createNotFoundException();
        }

        try {
            $data = Json::decode($request->getContent());
        } catch (\Throwable) {
            throw $this->createNotFoundException();
        }

        $bus->dispatch(new WingsWebhookMessage($project->getId(), $data));

        return new JsonResponse(['success' => true]);
    }
}
