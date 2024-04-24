<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Entity\Website\PetitionLocalized;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/petition_localized')]
class PetitionLocalizedController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/{id}/edit', name: 'console_website_petition_localized_edit', methods: ['GET'])]
    public function edit(PetitionLocalized $petitionLocalized): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        return $this->render('console/project/website/petition_localized/edit.html.twig', [
        ]);

    }

    #[Route('/{id}/delete', name: 'console_website_petition_localized_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, PetitionLocalized $petitionLocalized, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($petitionLocalized->getPetition());

        return $this->redirectToRoute('console_website_petitions', ['projectUuid' => $this->getProject()->getUuid()]);
    }
}