<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Entity\Website\Petition;
use App\Entity\Website\PetitionLocalized;
use App\Form\Website\LaunchPetitionType;
use App\Form\Website\Model\LaunchPetitionData;
use App\Form\Website\PetitionType;
use App\Platform\Permissions;
use App\Repository\Website\PetitionRepository;
use App\Repository\Website\TrombinoscopePersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/project/{projectUuid}/website/petitions')]
class PetitionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PetitionRepository $repository,
        private readonly TrombinoscopePersonRepository $trombinoscopePersonRepository,
    ) {
    }

    #[Route('', name: 'console_website_petitions')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $project = $this->getProject();
        $currentPage = $request->query->getInt('p', 1);

        return $this->render('console/project/website/petition/index.html.twig', [
            'petitions' => $this->repository->getPaginator($project, $currentPage),
            'current_page' => $currentPage,
            'project' => $project,
        ]);
    }

    #[Route('/create', name: 'console_website_petition_create')]
    public function create(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $data = new LaunchPetitionData();

        $form = $this->createForm(LaunchPetitionType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($data);exit;
        }

        return $this->render('console/project/website/petition/create.html.twig', [
            'form' => $form,
        ]);

        $initialTitle = $translator->trans('create.title', [], 'project_petitions');

        $petition = new Petition($this->getProject());
        $petition->setSlug((new AsciiSlugger())->slug($initialTitle)->lower()); // a temporary slug
        $this->em->persist($petition);
        $this->em->flush();

        $initialFormTitle = $translator->trans('create.form.title', [], 'project_petitions');

        $localized = new PetitionLocalized(
            $petition,
            $initialTitle,
            $initialFormTitle,
            $this->getProject()->getWebsiteLocale() // set project locale as default locale
        );
        $this->em->persist($localized);
        $this->em->flush();

        return $this->redirectToRoute('console_website_petition_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $petition->getUuid(),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_website_petition_edit')]
    public function edit(Request $request, Petition $petition): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petition);

        $form = $this->createForm(PetitionType::class, $petition, [
            'authors' => $this->trombinoscopePersonRepository->getProjectPersonsList($this->getProject()),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($petition);
            $this->em->flush();

            return $this->redirectToRoute('console_website_petitions', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        return $this->render('console/project/website/petition/edit.html.twig', [
            'petition' => $petition,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/delete', name: 'console_website_petition_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Petition $petition, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petition);

        $this->em->remove($petition);
        $this->em->flush();

        return $this->redirectToRoute('console_website_petitions', ['projectUuid' => $this->getProject()->getUuid()]);
    }
}
