<?php

namespace App\Controller\Api\Website;

use App\Api\Transformer\Website\EventTransformer;
use App\Controller\Api\AbstractApiController;
use App\Entity\Project;
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
        /** @var Project $project */
        $project = $this->getUser();

        $events = $this->repository->getApiEvents(
            project: $project,
            category: $request->query->get('category'),
            participant: $request->query->get('participant'),
            archived: $request->query->getBoolean('archived'),
            currentPage: $this->apiQueryParser->getPage(),
            limit: $this->apiQueryParser->getLimit() ?: $project->getWebsiteTheme()?->getEventsPerPage() ?: 12,
        );

        return $this->handleApiCollection($events, $transformer, true);
    }

    #[Route('/events/{id}', name: 'api_website_events_view', methods: ['GET'])]
    public function view(EventTransformer $transformer, string $id)
    {
        if (!$event = $this->repository->findOneByBase62UidOrSlug($this->getUser(), $id)) {
            throw $this->createNotFoundException();
        }

        $this->denyUnlessSameProject($event);

        if ($event->isOnlyForMembers()) {
            throw $this->createNotFoundException();
        }

        return $this->handleApiItem($event, $transformer);
    }
}
