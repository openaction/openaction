<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\PageCategoryTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\PageCategoryRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class PageCategoryController extends AbstractApiController
{
    private PageCategoryRepository $repository;

    public function __construct(PageCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/pages-categories', name: 'api_website_pages_categories_list', methods: ['GET'])]
    public function list(PageCategoryTransformer $transformer)
    {
        $categories = $this->repository->getProjectCategories($this->getUser());

        return $this->handleApiCollection($categories, $transformer, false);
    }

    #[Route('/pages-categories/{id}', name: 'api_website_pages_categories_view', methods: ['GET'])]
    public function view(PageCategoryTransformer $transformer, string $id)
    {
        if (!$category = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($category);

        return $this->handleApiItem($category, $transformer);
    }
}
