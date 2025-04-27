<?php

namespace App\Controller\Bridge;

use App\Controller\AbstractController;
use App\Proxy\DomainRouter;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListUnsubscribeController extends AbstractController
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
        private readonly EmailingCampaignMessageRepository $messageRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly DomainRouter $domainRouter,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/webhook/list-unsubscribe/{contactUuid}', name: 'webhook_list_unsubscribe', methods: ['POST'], stateless: true)]
    public function webhook(string $contactUuid): Response
    {
        $contact = $this->contactRepository->findOneByBase62Uid($contactUuid);
        $message = null;

        if (!$contact) {
            $message = $this->messageRepository->find($contactUuid);
            if (!$message) {
                throw $this->createNotFoundException();
            }

            $contact = $message->getContact();
        }

        $contact->updateNewsletterSubscription(subscribed: false, source: 'list:unsubscribe');
        $this->em->persist($contact);

        if ($message) {
            $message->markUnsubscribed();
            $this->em->persist($message);
        }

        $this->em->flush();

        if ($project = $this->projectRepository->findMainWebsiteProjectForOrganization($contact->getOrganization())) {
            return new RedirectResponse($this->domainRouter->generateUrl($project, '/newsletter?unsubscribe=1'));
        }

        return new RedirectResponse('https://citipo.com/en/pages/4VFkHDAvrV9zFAhdSbe1Zx/unsubscribe');
    }
}
