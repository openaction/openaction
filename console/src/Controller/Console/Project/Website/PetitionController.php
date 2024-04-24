<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Entity\Website\Petition;
use App\Entity\Website\PetitionLocalized;
use App\Form\Website\PetitionType;
use App\Platform\Permissions;
use App\Repository\Website\PetitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/project/{projectUuid}/website/petitions')]
class PetitionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PetitionRepository $repository,
    ) {
    }

    #[Route('', name: 'console_website_petitions')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/website/petition/index.html.twig', [
            'petitions' => $this->repository->getPaginator($project, $currentPage, 10),
            'current_page' => $currentPage,
            'project' => $project,
        ]);
    }

    #[Route('/create', name: 'console_website_petition_create')]
    public function create(Request $request, TranslatorInterface $translator): RedirectResponse
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $petition = new Petition($this->getProject());
        $petition->setSlug('test'); // TODO!! How to set the slug?
        $this->em->persist($petition);
        $this->em->flush();

        $localized = new PetitionLocalized(
            $petition,
            $translator->trans('create.title', [], 'project_petitions'),
            $this->getProject()->getWebsiteLocale()
        );
        $this->em->persist($localized);
        $this->em->flush();

        return $this->redirectToRoute('console_website_petition_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $petition->getUuid(),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_website_petition_edit', methods: ['GET'])]
    public function edit(Petition $petition): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petition);

        return $this->render('console/project/website/petition/edit.html.twig', [
            'petition' => $petition,
            'form' => $this->createForm(PetitionType::class)->createView(),
        ]);
    }

    #[Route('/{uuid}/delete', name: 'console_website_petition_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Petition $petition, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petition);

        return $this->redirectToRoute('console_website_petitions', ['projectUuid' => $this->getProject()->getUuid()]);
    }
}
