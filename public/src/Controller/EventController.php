<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/events")
 */
class EventController extends AbstractController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
    }

    /**
     * @Route("", name="event_list")
     */
    public function list(Request $request)
    {
        $this->denyUnlessToolEnabled('website_events');

        $page = $request->query->getInt('p', 1);
        $category = $request->query->get('c');

        return $this->render('events/list.html.twig', [
            'current_page' => $page,
            'current_category' => $category,
            'events' => $this->citipo->getEvents(
                $this->getApiToken(),
                $page,
                $category,
                $request->query->get('participant'),
                $request->query->getBoolean('archived'),
            ),
            'categories' => $this->citipo->getEventsCategories($this->getApiToken()),
        ]);
    }

    /**
     * @Route("/{id}/{slug}", name="event_view")
     */
    public function view(string $id, string $slug)
    {
        $this->denyUnlessToolEnabled('website_events');

        $event = $this->citipo->getEvent($this->getApiToken(), $id);

        if (!$event) {
            throw $this->createNotFoundException();
        }

        if ($event->slug !== $slug) {
            return $this->redirectToRoute('event_view', ['id' => $id, 'slug' => $event->slug], Response::HTTP_MOVED_PERMANENTLY);
        }

        if ($event->externalUrl) {
            return $this->redirect($event->externalUrl);
        }

        return $this->render('events/view.html.twig', [
            'event' => $event,
        ]);
    }
}
