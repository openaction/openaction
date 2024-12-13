<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\TrombinoscopePersonFullTransformer;
use App\Api\Transformer\Website\TrombinoscopePersonPartialTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\TrombinoscopePersonRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class TrombinoscopeController extends AbstractApiController
{
    private TrombinoscopePersonRepository $repository;

    public function __construct(TrombinoscopePersonRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/trombinoscope', name: 'api_website_trombinoscope_list', methods: ['GET'])]
    public function list(TrombinoscopePersonPartialTransformer $transformer, Request $request)
    {
        $persons = $this->repository->getApiPersons($this->getUser(), $request->query->get('category'));

        return $this->handleApiCollection($persons, $transformer, false);
    }

    #[Route('/trombinoscope/{id}', name: 'api_website_trombinoscope_view', methods: ['GET'])]
    public function view(TrombinoscopePersonFullTransformer $transformer, string $id)
    {
        if (!$person = $this->repository->findOneByBase62UidOrSlug($this->getUser(), $id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($person);

        return $this->handleApiItem($person, $transformer);
    }
}
