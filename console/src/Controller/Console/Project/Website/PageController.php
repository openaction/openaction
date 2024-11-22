<?php

namespace App\Controller\Console\Project\Website;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Controller\Util\ContentEditorUploadControllerTrait;
use App\DataManager\PageDataManager;
use App\Entity\Website\Page;
use App\Form\Project\Model\MoveEntityData;
use App\Form\Project\MoveEntityType;
use App\Form\Website\Model\PageData;
use App\Form\Website\Model\PageImageData;
use App\Form\Website\PageImageType;
use App\Form\Website\PageType;
use App\Platform\Permissions;
use App\Proxy\DomainRouter;
use App\Repository\Website\PageCategoryRepository;
use App\Repository\Website\PageRepository;
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

#[Route('/console/project/{projectUuid}/website/pages')]
class PageController extends AbstractController
{
    use ApiControllerTrait;
    use ContentEditorUploadControllerTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PageRepository $repository,
        private readonly PageCategoryRepository $categoryRepository,
        private readonly MessageBusInterface $bus,
        private readonly DomainRouter $domainRouter,
    ) {
    }

    #[Route('', name: 'console_website_pages')]
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        $project = $this->getProject();
        $currentCategory = $request->query->getInt('c');
        $currentPage = $request->query->getInt('p', 1);

        return $this->render('console/project/website/page/index.html.twig', [
            'pages' => $this->repository->getPaginator($project, $currentCategory, $currentPage),
            'categories' => $this->categoryRepository->getProjectCategories($project),
            'current_category' => $currentCategory,
            'current_page' => $currentPage,
            'project' => $project,
        ]);
    }

    #[Route('/create', name: 'console_website_page_create')]
    public function create(EntityManagerInterface $manager, Request $request, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $page = new Page($this->getProject(), $translator->trans('create.title', [], 'project_pages'));

        $manager->persist($page);
        $manager->flush();

        $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($page));

        return $this->redirectToRoute('console_website_page_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $page->getUuid(),
        ]);
    }

    #[Route('/{uuid}/duplicate', name: 'console_website_page_duplicate', methods: ['GET'])]
    public function duplicate(PageDataManager $dataManager, Request $request, Page $page)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($page);

        $duplicated = $dataManager->duplicate($page);

        $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($duplicated));

        return $this->redirectToRoute('console_website_page_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $duplicated->getUuid(),
        ]);
    }

    #[Route('/{uuid}/move', name: 'console_website_page_move', methods: ['GET', 'POST'])]
    public function move(PageDataManager $dataManager, Page $page, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($page);

        $data = new MoveEntityData();

        $form = $this->createForm(MoveEntityType::class, $data, [
            'user' => $this->getUser(),
            'permission' => Permissions::WEBSITE_PAGES_MANAGE,
            'current_project' => $this->getProject(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted(Permissions::WEBSITE_PAGES_MANAGE, $data->into)) {
                throw $this->createNotFoundException();
            }

            $dataManager->move($page, $data->into);

            $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($page));

            $this->addFlash('success', 'move.success');

            return $this->redirectToRoute('console_website_pages', [
                'projectUuid' => $data->into->getUuid(),
            ]);
        }

        return $this->render('console/project/website/page/move.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_website_page_edit', methods: ['GET'])]
    public function edit(Page $page)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($page);

        return $this->render('console/project/website/page/edit.html.twig', [
            'page' => $page,
            'form' => $this->createForm(PageType::class)->createView(),
            'categories' => $this->categoryRepository->getProjectCategories($this->getProject(), Query::HYDRATE_ARRAY),
            'available_parent_pages' => $this->repository->getAvailableParentPages($this->getProject(), $page),
            'image_form' => $this->createForm(PageImageType::class, new PageImageData())->createView(),
        ]);
    }

    #[Route('/{uuid}/update/content', name: 'console_website_page_update_content', methods: ['POST'])]
    public function updateContent(Page $page, Request $request)
    {
        return $this->updatePage($page, $request, 'Default');
    }

    #[Route('/{uuid}/update/metadata', name: 'console_website_page_update_metadata', methods: ['POST'])]
    public function updateMetadata(Page $page, Request $request)
    {
        return $this->updatePage($page, $request, 'Metadata');
    }

    private function updatePage(Page $page, Request $request, string $groupValidation)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($page);

        $pageData = new PageData();

        $form = $this->createForm(PageType::class, $pageData, ['validation_groups' => $groupValidation]);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ('Default' === $groupValidation) {
            $page->applyContentUpdate($pageData);
        } elseif ('Metadata' === $groupValidation) {
            $page->applyMetadataUpdate($pageData);

            $page->setParent(
                $pageData->parentId
                    ? $this->repository->findOneBy(['project' => $this->getProject(), 'id' => (int) $pageData->parentId])
                    : null
            );
        }

        $this->em->persist($page);
        $this->em->flush();

        if ('Metadata' === $groupValidation) {
            $this->categoryRepository->updateCategories($page, $pageData->getCategoriesArray());
        }

        $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($page));

        $id = Uid::toBase62($page->getUuid());

        return new JsonResponse([
            'success' => true,
            'share_url' => $this->domainRouter->generateShareUrl($page->getProject(), 'page', $id, $page->getSlug()),
        ]);
    }

    #[Route('/{uuid}/update/image', name: 'console_website_page_update_image')]
    public function updateImage(Page $page, CdnUploader $uploader, CdnRouter $cdnRouter, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($page);

        $data = new PageImageData();

        $form = $this->createForm(PageImageType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->repository->replaceImage($page, $uploader->upload(
            CdnUploadRequest::createWebsiteContentMainImageRequest($page->getProject(), $data->file)
        ));

        $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($page));

        return new JsonResponse(['image' => $cdnRouter->generateUrl($page->getImage())]);
    }

    #[Route('/{uuid}/content/upload', name: 'console_website_page_upload_image', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $router, Page $page, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($page);

        $uploadedFile = $this->createContentEditorUploadedFile($request);
        $upload = $uploader->upload(CdnUploadRequest::createWebsiteContentImageRequest($page->getProject(), $uploadedFile));

        return $this->createContentEditorUploadResponse($request->query->getInt('count'), $router->generateUrl($upload));
    }

    #[Route('/{uuid}/delete', name: 'console_website_page_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Page $page, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($page);

        $manager->remove($page);
        $manager->flush();

        $this->bus->dispatch(RemoveCmsDocumentMessage::forSearchable($page));

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_pages', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/{uuid}/view', name: 'console_website_page_view', methods: ['GET'])]
    public function view(DomainRouter $domainRouter, Page $page)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($page);

        return $this->redirect($domainRouter->generateRedirectUrl($this->getProject(), 'page', Uid::toBase62($page->getUuid())));
    }
}
