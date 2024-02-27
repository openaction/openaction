<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\PostFullTransformer;
use App\Api\Transformer\Website\PostPartialTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\PostRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class PostController extends AbstractApiController
{
    private PostRepository $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/posts', name: 'api_website_posts_list', methods: ['GET'])]
    public function list(PostPartialTransformer $transformer, Request $request)
    {
        $currentPage = $this->apiQueryParser->getPage();
        $posts = $this->repository->getApiPosts(
            project: $this->getUser(),
            category: $request->query->get('category'),
            author: $request->query->get('author'),
            currentPage: $currentPage,
        );

        return $this->handleApiCollection($posts, $transformer, true);
    }

    #[Route('/posts/{id}', name: 'api_website_posts_view', methods: ['GET'])]
    public function view(PostFullTransformer $transformer, string $id)
    {
        if (!$post = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($post);

        if ($post->isOnlyForMembers()) {
            throw $this->createNotFoundException();
        }

        return $this->handleApiItem($post, $transformer);
    }
}
