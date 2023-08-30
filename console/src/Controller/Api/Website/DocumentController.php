<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\DocumentTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\DocumentRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class DocumentController extends AbstractApiController
{
    private DocumentRepository $repository;

    public function __construct(DocumentRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/documents', name: 'api_website_documents_list', methods: ['GET'])]
    public function list(DocumentTransformer $transformer)
    {
        $documents = $this->repository->getApiDocuments($this->getUser());

        return $this->handleApiCollection($documents, $transformer, false);
    }

    #[Route('/documents/{id}', name: 'api_website_documents_view', methods: ['GET'])]
    public function view(DocumentTransformer $transformer, string $id)
    {
        if (!$document = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($document);

        return $this->handleApiItem($document, $transformer);
    }
}
