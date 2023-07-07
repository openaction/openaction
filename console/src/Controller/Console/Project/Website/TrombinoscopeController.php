<?php

namespace App\Controller\Console\Project\Website;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Controller\Util\ContentEditorUploadControllerTrait;
use App\DataManager\TrombinoscopeDataManager;
use App\Entity\Website\TrombinoscopePerson;
use App\Form\Project\Model\MoveEntityData;
use App\Form\Project\MoveEntityType;
use App\Form\Website\Model\TrombinoscopePersonData;
use App\Form\Website\Model\TrombinoscopePersonImageData;
use App\Form\Website\TrombinoscopePersonImageType;
use App\Form\Website\TrombinoscopePersonType;
use App\Platform\Permissions;
use App\Proxy\DomainRouter;
use App\Repository\Website\TrombinoscopeCategoryRepository;
use App\Repository\Website\TrombinoscopePersonRepository;
use App\Util\Json;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/project/{projectUuid}/website/trombinoscope')]
class TrombinoscopeController extends AbstractController
{
    use ApiControllerTrait;
    use ContentEditorUploadControllerTrait;

    private TrombinoscopePersonRepository $repository;
    private TrombinoscopeCategoryRepository $categoryRepository;

    public function __construct(TrombinoscopePersonRepository $r, TrombinoscopeCategoryRepository $cr)
    {
        $this->repository = $r;
        $this->categoryRepository = $cr;
    }

    #[Route('', name: 'console_website_trombinoscope')]
    public function index()
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/website/trombinoscope/index.html.twig', [
            'persons' => $this->repository->getConsolePersons($this->getProject()),
        ]);
    }

    #[Route('/sort', name: 'console_website_trombinoscope_sort', methods: ['POST'])]
    public function sort(Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $this->getProject());
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

    #[Route('/{uuid}/edit', name: 'console_website_trombinoscope_edit', methods: ['GET'])]
    public function edit(TrombinoscopePerson $person)
    {
        if ($person->isPublished()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $this->getProject());
        } else {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED, $this->getProject());
        }

        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($person);

        return $this->render('console/project/website/trombinoscope/edit.html.twig', [
            'person' => $person,
            'categories' => $this->categoryRepository->getProjectCategories($this->getProject(), Query::HYDRATE_ARRAY),
            'form' => $this->createForm(TrombinoscopePersonType::class, new TrombinoscopePersonData())->createView(),
            'image_form' => $this->createForm(TrombinoscopePersonImageType::class, new TrombinoscopePersonImageData())->createView(),
        ]);
    }

    #[Route('/{uuid}/update/content', name: 'console_website_trombinoscope_update_content', methods: ['POST'])]
    public function updateContent(TrombinoscopePerson $person, EntityManagerInterface $manager, Request $request)
    {
        return $this->applyUpdate($person, $manager, $request, 'Default');
    }

    #[Route('/{uuid}/update/metadata', name: 'console_website_trombinoscope_update_metadata', methods: ['POST'])]
    public function updateMetadata(TrombinoscopePerson $person, EntityManagerInterface $manager, Request $request)
    {
        return $this->applyUpdate($person, $manager, $request, 'Metadata');
    }

    private function applyUpdate(TrombinoscopePerson $person, EntityManagerInterface $manager, Request $request, string $groupValidation)
    {
        if ($person->isPublished()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $this->getProject());
        } else {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED, $this->getProject());
        }

        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($person);

        $data = new TrombinoscopePersonData();

        $form = $this->createForm(TrombinoscopePersonType::class, $data, ['validation_groups' => $groupValidation]);
        $form->handleRequest($request);

        // Ensure the user is authorized to publish
        if ($person->isPublished() !== $data->isPublished()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_PUBLISH, $this->getProject());
        }

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ('Default' === $groupValidation) {
            $person->applyContentUpdate($data);
        } elseif ('Metadata' === $groupValidation) {
            $person->applyMetadataUpdate($data);
        }

        $manager->persist($person);
        $manager->flush();

        if ('Metadata' === $groupValidation) {
            $this->categoryRepository->updateCategories($person, $data->getCategoriesArray());
        }

        return new JsonResponse(['success' => true], 200);
    }

    #[Route('/{uuid}/update/image', name: 'console_website_trombinoscope_update_image')]
    public function updateImage(TrombinoscopePerson $person, CdnUploader $uploader, CdnRouter $cdnRouter, Request $request)
    {
        if ($person->isPublished()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $this->getProject());
        } else {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED, $this->getProject());
        }

        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($person);

        $data = new TrombinoscopePersonImageData();

        $form = $this->createForm(TrombinoscopePersonImageType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->repository->replaceImage($person, $uploader->upload(
            CdnUploadRequest::createWebsiteTrombinoscopeImageRequest($person->getProject(), $data->file)
        ));

        return new JsonResponse(['image' => $cdnRouter->generateUrl($person->getImage())]);
    }

    #[Route('/{uuid}/content/upload', name: 'console_website_trombinoscope_upload_image', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $router, TrombinoscopePerson $person, Request $request)
    {
        if ($person->isPublished()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $this->getProject());
        } else {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED, $this->getProject());
        }

        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($person);

        $uploadedFile = $this->createContentEditorUploadedFile($request);
        $upload = $uploader->upload(CdnUploadRequest::createWebsiteContentImageRequest($person->getProject(), $uploadedFile));

        return $this->createContentEditorUploadResponse($request->query->getInt('count'), $router->generateUrl($upload));
    }

    #[Route('/{uuid}/delete', name: 'console_website_trombinoscope_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, TrombinoscopePerson $person, Request $request)
    {
        if ($person->isPublished()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $this->getProject());
        } else {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED, $this->getProject());
        }

        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($person);

        $manager->remove($person);
        $manager->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_trombinoscope', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/create', name: 'console_website_trombinoscope_create')]
    public function create(EntityManagerInterface $manager, Request $request, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $person = new TrombinoscopePerson(
            $this->getProject(),
            $translator->trans('create.title', [], 'project_trombinoscope'),
            1 + $this->repository->count(['project' => $this->getProject()])
        );

        $manager->persist($person);
        $manager->flush();

        return $this->redirectToRoute('console_website_trombinoscope_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $person->getUuid(),
        ]);
    }

    #[Route('/{uuid}/duplicate', name: 'console_website_trombinoscope_duplicate', methods: ['GET'])]
    public function duplicate(TrombinoscopeDataManager $dataManager, Request $request, TrombinoscopePerson $person)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($person);

        $duplicated = $dataManager->duplicate($person);

        return $this->redirectToRoute('console_website_trombinoscope_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $duplicated->getUuid(),
        ]);
    }

    #[Route('/{uuid}/move', name: 'console_website_trombinoscope_move', methods: ['GET', 'POST'])]
    public function move(TrombinoscopeDataManager $dataManager, TrombinoscopePerson $person, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($person);

        $data = new MoveEntityData();

        $form = $this->createForm(MoveEntityType::class, $data, [
            'user' => $this->getUser(),
            'permission' => Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED,
            'current_project' => $this->getProject(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED, $data->into)) {
                throw $this->createNotFoundException();
            }

            $dataManager->move($person, $data->into);

            $this->addFlash('success', 'move.success');

            return $this->redirectToRoute('console_website_trombinoscope', [
                'projectUuid' => $data->into->getUuid(),
            ]);
        }

        return $this->render('console/project/website/trombinoscope/move.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/view', name: 'console_website_trombinoscope_view', methods: ['GET'])]
    public function view(DomainRouter $domainRouter, TrombinoscopePerson $person)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($person);

        return $this->redirect($domainRouter->generateRedirectUrl($this->getProject(), 'trombinoscope', Uid::toBase62($person->getUuid())));
    }
}
