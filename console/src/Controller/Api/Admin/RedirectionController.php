<?php

namespace App\Controller\Api\Admin;

use App\Api\Payload\Admin\CreateRedirectionPayload;
use App\Controller\Api\AbstractApiController;
use App\Entity\Website\Redirection;
use App\Repository\Website\RedirectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Admin')]
#[Route('/api/admin')]
class RedirectionController extends AbstractApiController
{
    public function __construct(
        private readonly RedirectionRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/redirections', name: 'api_admin_redirections_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $payload = $this->createPayloadFromRequestContent($request, CreateRedirectionPayload::class);

        $this->em->persist($redirection = new Redirection(
            project: $this->getUser(),
            source: $payload->fromUrl,
            target: $payload->toUrl,
            code: $payload->type,
            weight: $payload->weight ?: 1 + $this->repository->count(['project' => $this->getUser()]),
        ));

        $this->em->flush();

        return new JsonResponse(
            data: [
                'fromUrl' => $redirection->getSource(),
                'toUrl' => $redirection->getTarget(),
                'type' => $redirection->getCode(),
            ],
            status: Response::HTTP_CREATED,
        );
    }
}
