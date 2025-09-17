<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\PetitionFullTransformer;
use App\Api\Transformer\Website\PetitionPartialTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\PetitionRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class PetitionController extends AbstractApiController
{
    public function __construct(private readonly PetitionRepository $repository)
    {
    }

    #[Route('/petitions', name: 'api_website_petitions_list', methods: ['GET'])]
    public function list(PetitionPartialTransformer $transformer, Request $request)
    {
        $petitions = $this->repository->getApiPetitions($this->getUser(), $request->query->get('category'));

        return $this->handleApiCollection($petitions, $transformer, false);
    }

    #[Route('/petitions/{id}', name: 'api_website_petitions_view', methods: ['GET'])]
    public function view(PetitionFullTransformer $transformer, string $id)
    {
        // For petitions, identifiers are slugs
        if (!$petition = $this->repository->findOneBySlug($this->getUser(), $id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($petition);

        if ($petition->isOnlyForMembers()) {
            throw $this->createNotFoundException();
        }

        return $this->handleApiItem($petition, $transformer);
    }
}
