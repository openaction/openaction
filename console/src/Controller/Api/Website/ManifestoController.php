<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\ManifestoTopicFullTransformer;
use App\Api\Transformer\Website\ManifestoTopicPartialTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\ManifestoTopicRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class ManifestoController extends AbstractApiController
{
    private ManifestoTopicRepository $repository;

    public function __construct(ManifestoTopicRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/manifesto', name: 'api_website_manifesto_list', methods: ['GET'])]
    public function list(ManifestoTopicPartialTransformer $transformer)
    {
        $topics = $this->repository->getApiTopics($this->getUser());

        return $this->handleApiCollection($topics, $transformer, false);
    }

    #[Route('/manifesto/{id}', name: 'api_website_manifesto_view', methods: ['GET'])]
    public function view(ManifestoTopicFullTransformer $transformer, string $id)
    {
        if (!$topic = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($topic);

        return $this->handleApiItem($topic, $transformer);
    }
}
