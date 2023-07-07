<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Entity\Website\PostCategory;
use App\Form\Website\PostCategoryType;
use App\Platform\Permissions;
use App\Repository\Website\PostCategoryRepository;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/posts/categories')]
class PostCategoryController extends AbstractController
{
    private PostCategoryRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(PostCategoryRepository $repo, EntityManagerInterface $em)
    {
        $this->repository = $repo;
        $this->em = $em;
    }

    #[Route('', name: 'console_website_posts_categories')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_CATEGORIES, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/website/post_category/index.html.twig', [
            'categories' => $this->repository->getProjectCategories($this->getProject()),
            'categories_count' => $this->repository->countPostsByProjectCategory($this->getProject()),
        ]);
    }

    #[Route('/create', name: 'console_website_post_category_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $category = new PostCategory($this->getProject(), '', 1 + $this->repository->count(['project' => $this->getProject()]));

        return $this->createOrEdit($category, $request, 'create.html.twig');
    }

    #[Route('/{uuid}/edit', name: 'console_website_post_category_edit', methods: ['GET', 'POST'])]
    public function edit(PostCategory $postCategory, Request $request): Response
    {
        $this->denyUnlessSameProject($postCategory);

        return $this->createOrEdit($postCategory, $request, 'edit.html.twig');
    }

    #[Route('/{uuid}/delete', name: 'console_website_post_category_delete', methods: ['GET'])]
    public function delete(PostCategory $postCategory, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_CATEGORIES, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($postCategory);

        $this->em->remove($postCategory);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_posts_categories', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/sort', name: 'console_website_post_category_sort', methods: ['POST'])]
    public function sort(Request $request): Response
    {
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

    private function createOrEdit(PostCategory $postCategory, Request $request, string $template): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_POSTS_MANAGE_CATEGORIES, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $form = $this->createForm(PostCategoryType::class, $postCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($postCategory);
            $this->em->flush();

            return $this->redirectToRoute('console_website_posts_categories', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        return $this->render('console/project/website/post_category/'.$template, [
            'form' => $form->createView(),
            'postCategory' => $postCategory,
        ]);
    }
}
