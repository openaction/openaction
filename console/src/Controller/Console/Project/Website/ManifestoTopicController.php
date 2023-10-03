<?php

namespace App\Controller\Console\Project\Website;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Controller\Util\ContentEditorUploadControllerTrait;
use App\DataManager\ManifestoDataManager;
use App\Entity\Website\ManifestoTopic;
use App\Form\Project\Model\MoveEntityData;
use App\Form\Project\MoveEntityType;
use App\Form\Website\ManifestoTopicImageType;
use App\Form\Website\ManifestoTopicPublishedAtType;
use App\Form\Website\ManifestoTopicType;
use App\Form\Website\Model\ManifestoTopicData;
use App\Form\Website\Model\ManifestoTopicImageData;
use App\Form\Website\Model\ManifestoTopicPublishedAtData;
use App\Platform\Permissions;
use App\Proxy\DomainRouter;
use App\Repository\Website\ManifestoTopicRepository;
use App\Util\Json;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/project/{projectUuid}/website/manifesto/topic')]
class ManifestoTopicController extends AbstractController
{
    use ApiControllerTrait;
    use ContentEditorUploadControllerTrait;

    private EntityManagerInterface $em;
    private ManifestoTopicRepository $repository;

    public function __construct(EntityManagerInterface $em, ManifestoTopicRepository $r)
    {
        $this->em = $em;
        $this->repository = $r;
    }

    #[Route('/sort', name: 'console_website_manifesto_sort_topics', methods: ['POST'])]
    public function sort(Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $data = Json::decode($request->request->get('data'));

        if (0 === count($data)) {
            throw new BadRequestHttpException('Invalid payload sort');
        }

        try {
            $this->repository->sort($data);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return new JsonResponse(['success' => 1]);
    }

    #[Route('/create', name: 'console_website_manifesto_topic_create')]
    public function create(TranslatorInterface $translator, Request $request)
    {
        $project = $this->getProject();
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $project);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $topic = new ManifestoTopic(
            $project,
            $translator->trans('create.topic.title', [], 'project_manifesto'),
            1 + $this->repository->count(['project' => $project])
        );

        $this->em->persist($topic);
        $this->em->flush();

        return $this->redirectToRoute('console_website_manifesto_topic_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $topic->getUuid(),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_website_manifesto_topic_edit')]
    public function edit(ManifestoTopic $topic, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_ENTITY, $topic);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($topic);

        $data = new ManifestoTopicData($topic);

        $form = $this->createForm(ManifestoTopicType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->applyUpdate($data);

            $this->em->persist($topic);
            $this->em->flush();
        }

        return $this->render('console/project/website/manifesto/editTopic.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
            'publication_form' => $this->createForm(ManifestoTopicPublishedAtType::class, new ManifestoTopicPublishedAtData())->createView(),
            'image_form' => $this->createForm(ManifestoTopicImageType::class, new ManifestoTopicImageData())->createView(),
        ]);
    }

    #[Route('/{uuid}/save', name: 'console_website_manifesto_topic_save', methods: ['POST'])]
    public function save(DomainRouter $domainRouter, ManifestoTopic $topic, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_ENTITY, $topic);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($topic);

        $data = new ManifestoTopicPublishedAtData();

        $form = $this->createForm(ManifestoTopicPublishedAtType::class, $data);
        $form->handleRequest($request);

        // Ensure the user is authorized to publish
        if ($topic->isPublished() !== $data->isPublication()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_PUBLISH, $this->getProject());
        }

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $topic->applyPublicationUpdate($data);

        $this->em->persist($topic);
        $this->em->flush();

        $id = Uid::toBase62($topic->getUuid());

        return new JsonResponse([
            'success' => true,
            'share_url' => $domainRouter->generateShareUrl($topic->getProject(), 'manifesto', $id, $topic->getSlug()),
        ]);
    }

    #[Route('/{uuid}/image', name: 'console_website_manifesto_topic_image', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $cdnRouter, ManifestoTopic $topic, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_ENTITY, $topic);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($topic);

        $data = new ManifestoTopicImageData();

        $form = $this->createForm(ManifestoTopicImageType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->repository->replaceImage($topic, $uploader->upload(
            CdnUploadRequest::createWebsiteManifestoMainImageRequest($topic->getProject(), $data->file)
        ));

        return new JsonResponse(['image' => $cdnRouter->generateUrl($topic->getImage())]);
    }

    #[Route('/{uuid}/duplicate', name: 'console_website_manifesto_topic_duplicate', methods: ['GET'])]
    public function duplicate(ManifestoDataManager $dataManager, ManifestoTopic $topic, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($topic);

        $duplicated = $dataManager->duplicate($topic);

        return $this->redirectToRoute('console_website_manifesto_topic_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $duplicated->getUuid(),
        ]);
    }

    #[Route('/{uuid}/move', name: 'console_website_manifesto_topic_move', methods: ['GET', 'POST'])]
    public function move(ManifestoDataManager $dataManager, ManifestoTopic $topic, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_PUBLISHED, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($topic);

        $data = new MoveEntityData();

        $form = $this->createForm(MoveEntityType::class, $data, [
            'user' => $this->getUser(),
            'permission' => Permissions::WEBSITE_MANIFESTO_MANAGE_PUBLISHED,
            'current_project' => $this->getProject(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_PUBLISHED, $data->into)) {
                throw $this->createNotFoundException();
            }

            $dataManager->move($topic, $data->into);

            $this->addFlash('success', 'move.success');

            return $this->redirectToRoute('console_website_manifesto', [
                'projectUuid' => $data->into->getUuid(),
            ]);
        }

        return $this->render('console/project/website/manifesto/moveTopic.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/delete', name: 'console_website_manifesto_topic_delete', methods: ['GET'])]
    public function delete(ManifestoTopic $topic, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_ENTITY, $topic);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($topic);

        $this->em->remove($topic);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_manifesto', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/{uuid}/view', name: 'console_website_manifesto_view', methods: ['GET'])]
    public function view(DomainRouter $domainRouter, ManifestoTopic $topic)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($topic);

        return $this->redirect($domainRouter->generateRedirectUrl($this->getProject(), 'manifesto', Uid::toBase62($topic->getUuid())));
    }
}
