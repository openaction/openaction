<?php

namespace App\Controller\Console\Project\Website;

use App\Cdn\CdnRouter;
use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Controller\Util\ContentEditorUploadControllerTrait;
use App\DataManager\PostDataManager;
use App\Entity\Project;
use App\Entity\Website\Post;
use App\Form\Project\CrosspostEntityType;
use App\Form\Project\Model\CrosspostEntityData;
use App\Form\Project\Model\MoveEntityData;
use App\Form\Project\MoveEntityType;
use App\Form\Website\Model\PostData;
use App\Form\Website\Model\PostImageData;
use App\Form\Website\PostImageType;
use App\Form\Website\PostType;
use App\Platform\Permissions;
use App\Proxy\DomainRouter;
use App\Repository\OrganizationMemberRepository;
use App\Repository\Website\PostCategoryRepository;
use App\Repository\Website\PostRepository;
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

#[Route('/console/project/{projectUuid}/website/posts')]
class PostController extends AbstractController
{
    use ApiControllerTrait;
    use ContentEditorUploadControllerTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PostRepository $repository,
        private readonly PostCategoryRepository $categoryRepository,
        private readonly TrombinoscopePersonRepository $trombinoscopePersonRepository,
        private readonly MessageBusInterface $bus,
        private readonly DomainRouter $domainRouter,
    ) {
    }

    #[Route('', name: 'console_website_posts')]
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        $project = $this->getProject();
        $currentCategory = $request->query->getInt('c');
        $currentPage = $request->query->getInt('p', 1);
        $currentQuery = $request->query->get('q');

        return $this->render('console/project/website/post/index.html.twig', [
            'posts' => $this->repository->getConsolePaginator($project, $currentQuery, $currentCategory, $currentPage),
            'categories' => $this->categoryRepository->getProjectCategories($project),
            'current_category' => $currentCategory,
            'current_page' => $currentPage,
            'current_query' => $currentQuery,
            'project' => $project,
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_website_post_edit', methods: ['GET'])]
    public function edit(Post $post)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_ENTITY, $post);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($post);

        return $this->render('console/project/website/post/edit.html.twig', [
            'post' => $post,
            'form' => $this->createForm(PostType::class, new PostData($post->getVideo()))->createView(),
            'availableAuthors' => $this->trombinoscopePersonRepository->getProjectPersonsList($this->getProject(), Query::HYDRATE_ARRAY),
            'categories' => $this->categoryRepository->getProjectCategories($this->getProject(), Query::HYDRATE_ARRAY),
            'image_form' => $this->createForm(PostImageType::class, new PostImageData())->createView(),
        ]);
    }

    #[Route('/{uuid}/update/content', name: 'console_website_post_update_content', methods: ['POST'])]
    public function updateContent(Post $post, Request $request)
    {
        return $this->updatePost($post, $request, 'Default');
    }

    #[Route('/{uuid}/update/metadata', name: 'console_website_post_update_metadata', methods: ['POST'])]
    public function updateMetadata(Post $post, Request $request)
    {
        return $this->updatePost($post, $request, 'Metadata');
    }

    private function updatePost(Post $post, Request $request, string $groupValidation)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_ENTITY, $post);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($post);

        $postData = new PostData($post->getVideo());

        $form = $this->createForm(PostType::class, $postData, ['validation_groups' => $groupValidation]);
        $form->handleRequest($request);

        // Ensure the user is authorized to publish a post
        if ($post->isPublished() !== $postData->isPublication()) {
            $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_PUBLISH, $this->getProject());
        }

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ('Default' === $groupValidation) {
            $post->applyContentUpdate($postData);
        } elseif ('Metadata' === $groupValidation) {
            $post->applyMetadataUpdate($postData);
        }

        $this->em->persist($post);
        $this->em->flush();

        if ('Metadata' === $groupValidation) {
            $this->trombinoscopePersonRepository->updateAuthors($post, $postData->getAuthorsArray());
            $this->categoryRepository->updateCategories($post, $postData->getCategoriesArray());
        }

        $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($post));

        $id = Uid::toBase62($post->getUuid());

        return new JsonResponse([
            'success' => true,
            'share_url' => $this->domainRouter->generateShareUrl($post->getProject(), 'post', $id, $post->getSlug()),
        ]);
    }

    #[Route('/{uuid}/update/image', name: 'console_website_post_update_image')]
    public function updateImage(Post $post, CdnUploader $uploader, CdnRouter $cdnRouter, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_ENTITY, $post);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($post);

        $data = new PostImageData();

        $form = $this->createForm(PostImageType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->createJsonApiFormProblemResponse($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->repository->replaceImage($post, $uploader->upload(
            CdnUploadRequest::createWebsiteContentMainImageRequest($post->getProject(), $data->file)
        ));

        $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($post));

        return new JsonResponse(['image' => $cdnRouter->generateUrl($post->getImage())]);
    }

    #[Route('/{uuid}/content/upload', name: 'console_website_post_upload_image', methods: ['POST'])]
    public function uploadImage(CdnUploader $uploader, CdnRouter $router, Post $post, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_ENTITY, $post);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($post);

        $uploadedFile = $this->createContentEditorUploadedFile($request);
        $upload = $uploader->upload(CdnUploadRequest::createWebsiteContentImageRequest($post->getProject(), $uploadedFile));

        return $this->createContentEditorUploadResponse($request->query->getInt('count'), $router->generateUrl($upload));
    }

    #[Route('/{uuid}/delete', name: 'console_website_post_delete', methods: ['GET'])]
    public function delete(Post $post, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_ENTITY, $post);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($post);

        $this->em->remove($post);
        $this->em->flush();

        $this->bus->dispatch(RemoveCmsDocumentMessage::forSearchable($post));

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_posts', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/create', name: 'console_website_post_create')]
    public function create(Request $request, OrganizationMemberRepository $memberRepository, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $post = new Post($this->getProject(), $translator->trans('create.title', [], 'project_posts'));

        $this->em->persist($post);
        $this->em->flush();

        $member = $memberRepository->findMember($this->getUser(), $this->getProject()->getOrganization());
        if ($categories = $member->getProjectsPermissions()->getCategoryPermissions($this->getProject()->getUuid()->toRfc4122(), 'posts')) {
            $this->categoryRepository->updateCategories($post, $categories);
        }

        return $this->redirectToRoute('console_website_post_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $post->getUuid(),
        ]);
    }

    #[Route('/{uuid}/duplicate', name: 'console_website_post_duplicate', methods: ['GET'])]
    public function duplicate(PostDataManager $dataManager, OrganizationMemberRepository $memberRepository, Request $request, Post $post)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($post);

        $duplicated = $dataManager->duplicate($post);

        $member = $memberRepository->findMember($this->getUser(), $this->getProject()->getOrganization());
        if ($categories = $member->getProjectsPermissions()->getCategoryPermissions($this->getProject()->getUuid()->toRfc4122(), 'posts')) {
            $this->categoryRepository->updateCategories($duplicated, $categories);
        }

        return $this->redirectToRoute('console_website_post_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $duplicated->getUuid(),
        ]);
    }

    #[Route('/{uuid}/move', name: 'console_website_post_move', methods: ['GET', 'POST'])]
    public function move(PostDataManager $dataManager, Post $post, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_PUBLISHED, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($post);

        $data = new MoveEntityData();

        $form = $this->createForm(MoveEntityType::class, $data, [
            'user' => $this->getUser(),
            'permission' => Permissions::WEBSITE_POSTS_MANAGE_PUBLISHED,
            'current_project' => $this->getProject(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted(Permissions::WEBSITE_POSTS_MANAGE_PUBLISHED, $data->into)) {
                throw $this->createNotFoundException();
            }

            $dataManager->move($post, $data->into);

            $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($post));

            $this->addFlash('success', 'move.success');

            return $this->redirectToRoute('console_website_posts', [
                'projectUuid' => $data->into->getUuid(),
            ]);
        }

        return $this->render('console/project/website/post/move.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/crosspost', name: 'console_website_post_crosspost', methods: ['GET', 'POST'])]
    public function crosspost(PostDataManager $dataManager, Post $post, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($post);

        $data = new CrosspostEntityData();

        $form = $this->createForm(CrosspostEntityType::class, $data, [
            'user' => $this->getUser(),
            'permission' => Permissions::WEBSITE_POSTS_MANAGE_DRAFTS,
            'current_project' => $this->getProject(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Project $project */
            foreach ($data->intoProjects as $project) {
                if (!$this->isGranted(Permissions::WEBSITE_POSTS_MANAGE_DRAFTS, $project)) {
                    continue;
                }

                $duplicate = $dataManager->duplicate($post);
                $dataManager->move($duplicate, $project);

                $this->bus->dispatch(UpdateCmsDocumentMessage::forSearchable($duplicate));
            }

            $this->addFlash('success', 'crosspost.success');

            return $this->redirectToRoute('console_website_posts', [
                'projectUuid' => $this->getProject()->getUuid(),
            ]);
        }

        return $this->render('console/project/website/post/crosspost.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/view', name: 'console_website_post_view', methods: ['GET'])]
    public function view(Post $post)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($post);

        return $this->redirect(
            $this->domainRouter->generateRedirectUrl($this->getProject(), 'post', Uid::toBase62($post->getUuid()))
        );
    }
}
