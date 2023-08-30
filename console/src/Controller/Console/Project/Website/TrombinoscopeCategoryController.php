<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Entity\Website\TrombinoscopeCategory;
use App\Form\Website\TrombinoscopeCategoryType;
use App\Platform\Permissions;
use App\Repository\Website\TrombinoscopeCategoryRepository;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/trombinoscope/categories')]
class TrombinoscopeCategoryController extends AbstractController
{
    private TrombinoscopeCategoryRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(TrombinoscopeCategoryRepository $repo, EntityManagerInterface $em)
    {
        $this->repo = $repo;
        $this->em = $em;
    }

    #[Route('', name: 'console_website_trombinoscope_categories')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_CATEGORIES, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/website/trombinoscope_category/index.html.twig', [
            'categories' => $this->repo->getProjectCategories($this->getProject()),
        ]);
    }

    #[Route('/create', name: 'console_website_trombinoscope_category_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $category = new TrombinoscopeCategory($this->getProject(), '', 1 + $this->repo->count(['project' => $this->getProject()]));

        return $this->createOrEdit($category, $request, 'create.html.twig');
    }

    #[Route('/{uuid}/edit', name: 'console_website_trombinoscope_category_edit', methods: ['GET', 'POST'])]
    public function edit(TrombinoscopeCategory $trombinoscopeCategory, Request $request): Response
    {
        $this->denyUnlessSameProject($trombinoscopeCategory);

        return $this->createOrEdit($trombinoscopeCategory, $request, 'edit.html.twig');
    }

    #[Route('/{uuid}/delete', name: 'console_website_trombinoscope_category_delete', methods: ['GET'])]
    public function delete(TrombinoscopeCategory $trombinoscopeCategory, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_CATEGORIES, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($trombinoscopeCategory);

        $this->em->remove($trombinoscopeCategory);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_website_trombinoscope_categories', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/sort', name: 'console_website_trombinoscope_category_sort', methods: ['POST'])]
    public function sort(Request $request): Response
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

    private function createOrEdit(TrombinoscopeCategory $trombinoscopeCategory, Request $request, string $template): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_CATEGORIES, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $form = $this->createForm(TrombinoscopeCategoryType::class, $trombinoscopeCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($trombinoscopeCategory);
            $this->em->flush();

            return $this->redirectToRoute('console_website_trombinoscope_categories', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        return $this->render('console/project/website/trombinoscope_category/'.$template, [
            'form' => $form->createView(),
            'trombinoscopeCategory' => $trombinoscopeCategory,
        ]);
    }
}
