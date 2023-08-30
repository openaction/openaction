<?php

namespace App\Controller\Api;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'General')]
class EntrypointController extends AbstractApiController
{
    /**
     * API entrypoint to check whether you are properly authenticated.
     */
    #[Route('/api', name: 'api_entrypoint', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Successful authentication message.')]
    public function entrypoint()
    {
        return new JsonResponse([
            'message' => 'You are authenticated to the Citipo API with the project '.$this->getUser()->getName(),
            'docs' => 'https://citipo.com',
        ]);
    }
}
