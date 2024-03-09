<?php

namespace App\Controller\Console\Project\Website;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\DataManager\EventDataManager;
use App\Entity\Website\Event;
use App\Entity\Website\Form;
use App\Entity\Website\FormBlock;
use App\Form\Project\Model\MoveEntityData;
use App\Form\Project\MoveEntityType;
use App\Form\Website\CreateEventType;
use App\Form\Website\EventImageType;
use App\Form\Website\Model\EventData;
use App\Form\Website\Model\EventImageData;
use App\Form\Website\UpdateEventType;
use App\Platform\Permissions;
use App\Proxy\DomainRouter;
use App\Repository\Website\EventCategoryRepository;
use App\Repository\Website\EventRepository;
use App\Repository\Website\TrombinoscopePersonRepository;
use App\Search\Consumer\RemoveCmsDocumentMessage;
use App\Search\Consumer\UpdateCmsDocumentMessage;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/project/{projectUuid}/website/events')]
class EventController extends AbstractController
{
    use ApiControllerTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventRepository $repo,
        private readonly EventCategoryRepository $categoryRepo,
        private readonly TrombinoscopePersonRepository $trombinoscopePersonRepository,
        private readonly DomainRouter $domainRouter,
        private readonly TranslatorInterface $translator,
        private readonly MessageBusInterface $bus,
    ) {
    }

    #[Route('', name: 'console_website_events')]
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $currentCategory = $request->query->getInt('c');
        $currentPage = $request->query->getInt('p', 1);

        return $this->render('console/project/website/event/index.html.twig', [
            'events' => $this->repo->getProjectPaginator($this->getProject(), $currentCategory, $currentPage),
            'project' => $this->getProject(),
            'categories' => $this->categoryRepo->getUsedCategoriesJson($this->getProject()),
            'current_category' => $currentCategory,
            'current_page' => $currentPage,
        ]);
    }

    #[Route('/create', name: 'console_website_event_create')]
    public function create(DomainRouter $domainRouter, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $event = new Event($this->getProject(), '');
        $eventData = new EventData($event);

        $form = $this->createForm(CreateEventType::class, $eventData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If requested, create the form and link it to the event
            if ($eventData->hasForm) {
                $form = new Form(
                    $this->getProject(),
                    $this->translator->trans('create.formBlocks.title', ['%title%' => $eventData->title], 'project_events')
                );

                $this->em->persist($form);

                $blocks = [
                    ['type' => FormBlock::TYPE_FIRST_NAME, 'content' => 'create.formBlocks.first_name'],
                    ['type' => FormBlock::TYPE_LAST_NAME, 'content' => 'create.formBlocks.last_name'],
                    ['type' => FormBlock::TYPE_EMAIL, 'content' => 'create.formBlocks.email'],
                    ['type' => FormBlock::TYPE_ZIP_CODE, 'content' => 'create.formBlocks.zip_code'],
                    ['type' => FormBlock::TYPE_CITY, 'content' => 'create.formBlocks.city'],
                    ['type' => FormBlock::TYPE_COUNTRY, 'content' => 'create.formBlocks.country'],
                ];

                foreach ($blocks as $block) {
                    $content = $this->translator->trans($block['content'], [], 'project_events');
                    $this->em->persist(new FormBlock($form, $block['type'], $content, true));
                }

                $this->em->flush();

                $event->setForm($form);

                $eventData->buttonText = $this->translator->trans('create.registerButton', [], 'project_events');
                $eventData->url = $domainRouter->generateRedirectUrl($this->getProject(), 'form', Uid::toBase62($form->getUuid()));
            }

            // Persist the event
            $event->applyContentUpdate($eventData);

            $this->em->persist($event);
            $this->em->flush();

            return $this->redirectToRoute('console_website_event_edit', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $event->getUuid(),
            ]);
        }

        return $this->render('console/project/website/event/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_website_event_edit')]
    public function edit(Event $event, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_ENTITY, $event);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($event);

        $eventData = new EventData($event);

        $form = $this->createForm(UpdateEventType::class, $eventData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Update the event
            $event->applyContentUpdate($eventData);

            $this->em->persist($event);
            $this->em->flush();

            $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($event));
        }

        return $this->render('console/project/website/event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'availableParticipants' => $this->trombinoscopePersonRepository->getProjectPersonsList($this->getProject(), Query::HYDRATE_ARRAY),
            'image_form' => $this->createForm(EventImageType::class, new EventImageData())->createView(),
            'categories' => $this->categoryRepo->getProjectCategories($this->getProject(), Query::HYDRATE_ARRAY),
        ]);
    }

    #[Route('/{uuid}/duplicate', name: 'console_website_event_duplicate', methods: ['GET'])]
    public function duplicate(EventDataManager $dataManager, Event $event, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($event);

        $duplicated = $dataManager->duplicate($event);

        return $this->redirectToRoute('console_website_event_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $duplicated->getUuid(),
        ]);
    }

    #[Route('/{uuid}/move', name: 'console_website_event_move', methods: ['GET', 'POST'])]
    public function move(EventDataManager $dataManager, Event $event, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_PUBLISHED, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($event);

        $data = new MoveEntityData();

        $form = $this->createForm(MoveEntityType::class, $data, [
            'user' => $this->getUser(),
            'permission' => Permissions::WEBSITE_EVENTS_MANAGE_PUBLISHED,
            'current_project' => $this->getProject(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted(Permissions::WEBSITE_EVENTS_MANAGE_PUBLISHED, $data->into)) {
                throw $this->createNotFoundException();
            }

            $dataManager->move($event, $data->into);

            $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($event));

            $this->addFlash('success', 'move.success');

            return $this->redirectToRoute('console_website_events', [
                'projectUuid' => $data->into->getUuid(),
            ]);
        }

        return $this->render('console/project/website/event/move.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/update/metadata', name: 'console_website_event_update_metadata', methods: ['POST'])]
    public function updateMetadata(Event $event, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_ENTITY, $event);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($event);

        $eventData = new EventData($event);

        $form = $this->createForm(UpdateEventType::class, $eventData, ['validation_groups' => 'Metadata']);
        $form->handleRequest($request);

        if ($event->isPublished() !== $eventData->isPublished()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_PUBLISH, $this->getProject());
        }

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $event->applyMetadataUpdate($eventData);

        $this->em->persist($event);
        $this->em->flush();

        $this->trombinoscopePersonRepository->updateParticipants($event, $eventData->getParticipantsArray());
        $this->categoryRepo->updateCategories($event, $eventData->getCategoriesArray());

        $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($event));

        $id = Uid::toBase62($event->getUuid());

        return new JsonResponse([
            'success' => true,
            'share_url' => $this->domainRouter->generateShareUrl($event->getProject(), 'event', $id, $event->getSlug()),
        ]);
    }

    #[Route('/{uuid}/update/image', name: 'console_website_event_update_image')]
    public function updateImage(Event $event, CdnUploader $uploader, CdnRouter $cdnRouter, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($event);

        $data = new EventImageData();

        $form = $this->createForm(EventImageType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->repo->replaceImage($event, $uploader->upload(
            CdnUploadRequest::createWebsiteContentMainImageRequest($event->getProject(), $data->file)
        ));

        $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($event));

        return new JsonResponse(['image' => $cdnRouter->generateUrl($event->getImage())]);
    }

    #[Route('/{uuid}/delete', name: 'console_website_event_delete')]
    public function delete(Event $event, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_ENTITY, $event);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($event);

        $this->bus->dispatch(RemoveCmsDocumentMessage::forSearchable($event));

        $this->em->remove($event);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_events', [
            'projectUuid' => $this->getProject()->getUuid(),
        ]);
    }

    #[Route('/{uuid}/view', name: 'console_website_event_view')]
    public function view(DomainRouter $domainRouter, Event $event)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($event);

        return $this->redirect($domainRouter->generateRedirectUrl($this->getProject(), 'event', Uid::toBase62($event->getUuid())));
    }
}
