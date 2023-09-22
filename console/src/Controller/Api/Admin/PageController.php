<?php

namespace App\Controller\Api\Admin;

use App\Api\Payload\Admin\CreatePagePayload;
use App\Api\Payload\Admin\UpdateMainImagePayload;
use App\Api\Transformer\Website\PagePartialTransformer;
use App\Cdn\CdnUploader;
use App\Controller\Api\AbstractApiController;
use App\Entity\Website\Page;
use App\Entity\Website\PageCategory;
use App\Repository\Website\PageCategoryRepository;
use App\Repository\Website\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Admin')]
#[Route('/api/admin')]
class PageController extends AbstractApiController
{
    public function __construct(
        private readonly PageRepository $repository,
        private readonly PageCategoryRepository $categoryRepository,
        private readonly PagePartialTransformer $transformer,
        private readonly EntityManagerInterface $em,
        private readonly CdnUploader $cdnUploader,
    ) {
    }

    #[Route('/pages', name: 'api_admin_pages_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $payload = $this->createPayloadFromRequestContent($request, CreatePagePayload::class);

        // Resolve categories
        $categories = [];
        foreach ($payload->categories ?: [] as $categoryName) {
            $category = $this->categoryRepository->findOneBy(['name' => $categoryName, 'project' => $this->getUser()]);

            // Persist missing categories on the fly
            if (!$category) {
                $category = new PageCategory($this->getUser(), $categoryName);
                $this->em->persist($category);
                $this->em->flush();
            }

            $categories[] = $category;
        }

        $page = new Page($this->getUser(), $payload->title);
        $page->applyAdminApiUpdate(
            content: $payload->content ?: '',
            description: $payload->description ?: null,
            categories: $categories,
        );

        $this->em->persist($page);
        $this->em->flush();

        return $this->handleApiItem($page, $this->transformer, status: Response::HTTP_CREATED);
    }

    #[Route('/pages/{id}/main-image', name: 'api_admin_pages_update_main_image', methods: ['PUT'])]
    public function updateMainImage(Request $request, string $id): Response
    {
        $page = $this->repository->findOneByBase62Uid($id);
        if (!$page || $page?->getProject()?->getId() !== $this->getUser()?->getId()) {
            throw $this->createNotFoundException();
        }

        $payload = $this->createPayloadFromRequestFiles($request, UpdateMainImagePayload::class);

        $page->setImage($this->cdnUploader->upload($payload->buildUploadRequestFor($this->getUser())));

        $this->em->persist($page);
        $this->em->flush();

        return $this->handleApiItem($page, $this->transformer, status: Response::HTTP_ACCEPTED);
    }
}
