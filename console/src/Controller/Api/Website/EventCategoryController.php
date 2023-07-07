<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\EventCategoryTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\EventCategoryRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class EventCategoryController extends AbstractApiController
{
    private EventCategoryRepository $repository;

    public function __construct(EventCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/events-categories', name: 'api_website_events_categories_list', methods: ['GET'])]
    public function list(EventCategoryTransformer $transformer)
    {
        $categories = $this->repository->getProjectCategories($this->getUser());

        return $this->handleApiCollection($categories, $transformer, false);
    }

    #[Route('/events-categories/{id}', name: 'api_website_events_categories_view', methods: ['GET'])]
    public function view(EventCategoryTransformer $transformer, string $id)
    {
        if (!$category = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($category);

        return $this->handleApiItem($category, $transformer);
    }
}
