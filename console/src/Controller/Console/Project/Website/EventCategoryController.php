<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Entity\Website\EventCategory;
use App\Form\Website\EventCategoryType;
use App\Platform\Permissions;
use App\Repository\Website\EventCategoryRepository;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/events/categories')]
class EventCategoryController extends AbstractController
{
    private EventCategoryRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EventCategoryRepository $eventCategoryRepository, EntityManagerInterface $manager)
    {
        $this->repo = $eventCategoryRepository;
        $this->em = $manager;
    }

    #[Route('', name: 'console_website_events_categories')]
    public function index()
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS, $project);
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/website/event_category/index.html.twig', [
            'categories' => $this->repo->getProjectCategories($this->getProject()),
        ]);
    }

    #[Route('/create', name: 'console_website_event_category_create', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        $category = new EventCategory($this->getProject(), '', 1 + $this->repo->count(['project' => $this->getProject()]));

        return $this->createOrEdit($category, $request, 'create.html.twig');
    }

    #[Route('/{uuid}/edit', name: 'console_website_event_category_edit', methods: ['GET', 'POST'])]
    public function edit(EventCategory $eventCategory, Request $request)
    {
        $this->denyUnlessSameProject($eventCategory);

        return $this->createOrEdit($eventCategory, $request, 'edit.html.twig');
    }

    #[Route('/{uuid}/delete', name: 'console_website_event_category_delete', methods: ['GET'])]
    public function delete(EventCategory $eventCategory, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($eventCategory);

        $this->em->remove($eventCategory);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_events_categories', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/sort', name: 'console_website_event_category_sort', methods: ['POST'])]
    public function sort(Request $request)
    {
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $data = Json::decode($request->request->get('data'));

        if (0 === count($data)) {
            throw new BadRequestHttpException('Invalid payload sort');
        }

        try {
            $this->repo->sort($data);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return new JsonResponse(['success' => 1]);
    }

    private function createOrEdit(EventCategory $eventCategory, Request $request, string $template)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $form = $this->createForm(EventCategoryType::class, $eventCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($eventCategory);
            $this->em->flush();

            return $this->redirectToRoute('console_website_events_categories', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        return $this->render('console/project/website/event_category/'.$template, [
            'form' => $form->createView(),
            'eventCategory' => $eventCategory,
        ]);
    }
}
