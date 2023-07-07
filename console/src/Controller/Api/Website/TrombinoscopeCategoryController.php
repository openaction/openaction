<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\TrombinoscopeCategoryTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\TrombinoscopeCategoryRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class TrombinoscopeCategoryController extends AbstractApiController
{
    private TrombinoscopeCategoryRepository $repository;

    public function __construct(TrombinoscopeCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/trombinoscope-categories', name: 'api_website_trombinoscope_categories_list', methods: ['GET'])]
    public function list(TrombinoscopeCategoryTransformer $transformer)
    {
        $categories = $this->repository->getProjectCategories($this->getUser());

        return $this->handleApiCollection($categories, $transformer, false);
    }

    #[Route('/trombinoscope-categories/{id}', name: 'api_website_trombinoscope_categories_view', methods: ['GET'])]
    public function view(TrombinoscopeCategoryTransformer $transformer, string $id)
    {
        if (!$category = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($category);

        return $this->handleApiItem($category, $transformer);
    }
}
