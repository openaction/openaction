<?php

namespace App\Controller\Bridge;

use App\Community\Webhook\ListUnsubscribeWebhookMessage;
use App\Controller\AbstractController;
use App\Proxy\DomainRouter;
use App\Repository\Community\ContactRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ListUnsubscribeController extends AbstractController
{
    public function __construct(
        private readonly ContactRepository $repository,
        private readonly ProjectRepository $projectRepository,
        private readonly DomainRouter $domainRouter,
        private readonly MessageBusInterface $bus,
    ) {
    }

    #[Route('/webhook/list-unsubscribe/{contactUuid}', name: 'webhook_list_unsubscribe', methods: ['POST'], stateless: true)]
    public function webhook(string $contactUuid): Response
    {
        $contact = $this->repository->findOneByBase62Uid($contactUuid);
        if (!$contact) {
            throw $this->createNotFoundException();
        }

        $this->bus->dispatch(new ListUnsubscribeWebhookMessage($contactUuid));

        if ($project = $this->projectRepository->findMainWebsiteProjectForOrganization($contact->getOrganization())) {
            return new RedirectResponse($this->domainRouter->generateUrl($project, '/newsletter?unsubscribe=1'));
        }

        return new RedirectResponse('https://citipo.com/en/pages/4VFkHDAvrV9zFAhdSbe1Zx/unsubscribe');
    }
}
