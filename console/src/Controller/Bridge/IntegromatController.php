<?php

namespace App\Controller\Bridge;

use App\Controller\AbstractController;
use App\Entity\Organization;
use App\Repository\Integration\IntegromatWebhookRepository;
use App\Repository\OrganizationRepository;
use App\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/webhook/integromat', stateless: true)]
class IntegromatController extends AbstractController
{
    private OrganizationRepository $organizationRepository;
    private IntegromatWebhookRepository $webhookRepository;

    public function __construct(OrganizationRepository $or, IntegromatWebhookRepository $wr)
    {
        $this->organizationRepository = $or;
        $this->webhookRepository = $wr;
    }

    #[Route('', name: 'webhook_integromat_index', stateless: true)]
    public function index(Request $request)
    {
        if (!$orga = $this->getTokenOrganization($request)) {
            return new JsonResponse(['error' => 'This Citipo-Integromat token is invalid'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['organization' => $orga->getName()]);
    }

    #[Route('/attach', name: 'webhook_integromat_attach', methods: ['POST'], stateless: true)]
    public function attach(Request $request)
    {
        if (!$orga = $this->getTokenOrganization($request)) {
            return new JsonResponse(['error' => 'This Citipo-Integromat token is invalid'], Response::HTTP_BAD_REQUEST);
        }

        if (!$url = Json::decode($request->getContent())['url'] ?? null) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $webhook = $this->webhookRepository->attachWebhook($orga, $url);

        return new JsonResponse(['token' => $webhook->getToken()]);
    }

    #[Route('/detach/{token}', name: 'webhook_integromat_detach', methods: ['POST'], stateless: true)]
    public function detach(Request $request, string $token)
    {
        if (!$orga = $this->getTokenOrganization($request)) {
            return new JsonResponse(['error' => 'This Citipo-Integromat token is invalid'], Response::HTTP_BAD_REQUEST);
        }

        $this->webhookRepository->detachWebhook($orga, $token);

        return new JsonResponse(['status' => 'detached']);
    }

    private function getTokenOrganization(Request $request): ?Organization
    {
        if (!$token = $request->headers->get('x-api-key')) {
            return null;
        }

        if (!$orga = $this->organizationRepository->findOneBy(['apiToken' => $token])) {
            return null;
        }

        return $orga;
    }
}
