<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Entity\Website\PetitionCategory;
use App\Form\Website\PetitionCategoryType;
use App\Platform\Permissions;
use App\Repository\Website\PetitionCategoryRepository;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/petitions/categories')]
class PetitionCategoryController extends AbstractController
{
    private PetitionCategoryRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(PetitionCategoryRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    #[Route('', name: 'console_website_petitions_categories')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_CATEGORIES, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/website/petition_category/index.html.twig', [
            'categories' => $this->repository->getPetitionCategoriesForProject($this->getProject()),
            'categories_count' => $this->repository->countPetitionsByCategoriesForProject($this->getProject()),
        ]);
    }

    #[Route('/create', name: 'console_website_petition_category_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $category = new PetitionCategory(
            $this->getProject(),
            '',
            1 + $this->repository->count(['project' => $this->getProject()])
        );

        return $this->createOrEdit($category, $request, 'create.html.twig');
    }

    #[Route('/{uuid}/edit', name: 'console_website_petition_category_edit', methods: ['GET', 'POST'])]
    public function edit(PetitionCategory $category, Request $request): Response
    {
        $this->denyUnlessSameProject($category);

        return $this->createOrEdit($category, $request, 'edit.html.twig');
    }

    #[Route('/{uuid}/delete', name: 'console_website_petition_category_delete', methods: ['GET'])]
    public function delete(PetitionCategory $category, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_CATEGORIES, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($category);

        $this->em->remove($category);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_petitions_categories', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/sort', name: 'console_website_petition_category_sort', methods: ['POST'])]
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

    private function createOrEdit(PetitionCategory $category, Request $request, string $template): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_CATEGORIES, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $form = $this->createForm(PetitionCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();

            return $this->redirectToRoute('console_website_petitions_categories', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        return $this->render('console/project/website/petition_category/'.$template, [
            'form' => $form->createView(),
        ]);
    }
}
