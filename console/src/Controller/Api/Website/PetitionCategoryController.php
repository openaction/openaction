<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\PetitionCategoryTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\PetitionCategoryRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class PetitionCategoryController extends AbstractApiController
{
    public function __construct(private readonly PetitionCategoryRepository $repository)
    {
    }

    #[Route('/petitions-categories', name: 'api_website_petitions_categories_list', methods: ['GET'])]
    public function list(PetitionCategoryTransformer $transformer)
    {
        $categories = $this->repository->getProjectCategories($this->getUser());

        return $this->handleApiCollection($categories, $transformer, false);
    }

    #[Route('/petitions-categories/{id}', name: 'api_website_petitions_categories_view', methods: ['GET'])]
    public function view(PetitionCategoryTransformer $transformer, string $id)
    {
        if (!$category = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($category);

        return $this->handleApiItem($category, $transformer);
    }
}
