<?php

namespace App\Controller\Api\Admin;

use App\Api\Payload\Admin\CreatePostPayload;
use App\Api\Payload\Admin\UpdateMainImagePayload;
use App\Api\Transformer\Website\PostPartialTransformer;
use App\Cdn\CdnUploader;
use App\Controller\Api\AbstractApiController;
use App\Entity\Website\Post;
use App\Entity\Website\PostCategory;
use App\Repository\Website\PostCategoryRepository;
use App\Repository\Website\PostRepository;
use App\Util\Video;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Admin')]
#[Route('/api/admin')]
class PostController extends AbstractApiController
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly PostCategoryRepository $categoryRepository,
        private readonly PostPartialTransformer $transformer,
        private readonly EntityManagerInterface $em,
        private readonly CdnUploader $cdnUploader,
    ) {
    }

    #[Route('/posts', name: 'api_admin_posts_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $payload = $this->createPayloadFromRequestContent($request, CreatePostPayload::class);

        // Resolve categories
        $categories = [];
        foreach ($payload->categories ?: [] as $categoryName) {
            $category = $this->categoryRepository->findOneBy(['name' => $categoryName, 'project' => $this->getUser()]);

            // Persist missing categories on the fly
            if (!$category) {
                $category = new PostCategory($this->getUser(), $categoryName);
                $this->em->persist($category);
                $this->em->flush();
            }

            $categories[] = $category;
        }

        $post = new Post($this->getUser(), $payload->title);
        $post->applyAdminApiUpdate(
            content: $payload->content ?: '',
            description: $payload->description ?: null,
            video: $payload->videoUrl ? Video::createFromUrl($payload->videoUrl)?->toReference() : null,
            quote: $payload->quote ?: null,
            author: $payload->author ?: null,
            publishedAt: $payload->publishedAt ? new \DateTime($payload->publishedAt) : null,
            categories: $categories,
        );

        $this->em->persist($post);
        $this->em->flush();

        return $this->handleApiItem($post, $this->transformer, status: Response::HTTP_CREATED);
    }

    #[Route('/posts/{id}/main-image', name: 'api_admin_posts_update_main_image', methods: ['PUT'])]
    public function updateMainImage(Request $request, string $id): Response
    {
        $post = $this->repository->findOneByBase62Uid($id);
        if (!$post || $post?->getProject()?->getId() !== $this->getUser()?->getId()) {
            throw $this->createNotFoundException();
        }

        $payload = $this->createPayloadFromRequestFiles($request, UpdateMainImagePayload::class);

        $post->setImage($this->cdnUploader->upload($payload->buildUploadRequestFor($this->getUser())));

        $this->em->persist($post);
        $this->em->flush();

        return $this->handleApiItem($post, $this->transformer, status: Response::HTTP_ACCEPTED);
    }
}
