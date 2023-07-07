<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\PostCategoryTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\PostCategoryRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class PostCategoryController extends AbstractApiController
{
    private PostCategoryRepository $repository;

    public function __construct(PostCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/posts-categories', name: 'api_website_posts_categories_list', methods: ['GET'])]
    public function list(PostCategoryTransformer $transformer)
    {
        $categories = $this->repository->getProjectCategories($this->getUser());

        return $this->handleApiCollection($categories, $transformer, false);
    }

    #[Route('/posts-categories/{id}', name: 'api_website_posts_categories_view', methods: ['GET'])]
    public function view(PostCategoryTransformer $transformer, string $id)
    {
        if (!$category = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($category);

        return $this->handleApiItem($category, $transformer);
    }
}
