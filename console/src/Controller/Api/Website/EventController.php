<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\EventTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Website\EventRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Website')]
#[Route('/api/website')]
class EventController extends AbstractApiController
{
    private EventRepository $repository;

    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/events', name: 'api_website_events_list', methods: ['GET'])]
    public function list(EventTransformer $transformer, Request $request)
    {
        $currentPage = $this->apiQueryParser->getPage();
        $events = $this->repository->getApiEvents($this->getUser(), $request->query->get('category'), $currentPage);

        return $this->handleApiCollection($events, $transformer, true);
    }

    #[Route('/events/{id}', name: 'api_website_events_view', methods: ['GET'])]
    public function view(EventTransformer $transformer, string $id)
    {
        if (!$event = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($event);

        if ($event->isOnlyForMembers()) {
            throw $this->createNotFoundException();
        }

        return $this->handleApiItem($event, $transformer);
    }
}
