<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\PageFullTransformer;
use App\Api\Transformer\Website\PagePartialTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\PageRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class PageController extends AbstractApiController
{
    private PageRepository $repository;

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/pages', name: 'api_website_pages_list', methods: ['GET'])]
    public function list(PagePartialTransformer $transformer, Request $request)
    {
        $pages = $this->repository->getApiPages($this->getUser(), $request->query->get('category'));

        return $this->handleApiCollection($pages, $transformer, false);
    }

    #[Route('/pages/{id}', name: 'api_website_pages_view', methods: ['GET'])]
    public function view(PageFullTransformer $transformer, string $id)
    {
        if (!$page = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($page);

        if ($page->isOnlyForMembers()) {
            throw $this->createNotFoundException();
        }

        return $this->handleApiItem($page, $transformer);
    }
}
