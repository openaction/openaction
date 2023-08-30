<?php

namespace App\Controller\Console\Organization\Community;

use App\Community\Ambiguity\ContactMerger;
use App\Controller\AbstractController;
use App\Entity\Community\Ambiguity;
use App\Platform\Permissions;
use App\Repository\Community\AmbiguityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}/community/ambiguities')]
class AmbiguityController extends AbstractController
{
    private AmbiguityRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(AmbiguityRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    #[Route('', name: 'console_organization_community_ambiguities')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/organization/community/ambiguity/list.html.twig', [
            'ambiguities' => $this->repository->findByOrganization($this->getOrganization()),
        ]);
    }

    #[Route('/{id}/merge/{type}', name: 'console_organization_community_ambiguities_merge', requirements: ['type' => 'oldest|newest'])]
    public function merge(ContactMerger $merger, Ambiguity $ambiguity, Request $request, string $type): Response
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $merger->merge($ambiguity, $type);

        $this->addFlash('success', 'ambiguities.merge_success');

        return $this->redirectToRoute('console_organization_community_ambiguities', [
            'organizationUuid' => $this->getOrganization()->getUuid(),
        ]);
    }

    #[Route('/{id}/ignore', name: 'console_organization_community_ambiguities_ignore')]
    public function ignore(Ambiguity $ambiguity, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $ambiguity->updateIgnoredAt($ambiguity->getIgnoredAt() instanceof \DateTime ? null : new \DateTime());

        $this->em->persist($ambiguity);
        $this->em->flush();

        $this->addFlash('success', 'ambiguities.ignore_success');

        return $this->redirectToRoute('console_organization_community_ambiguities', [
            'organizationUuid' => $this->getOrganization()->getUuid(),
        ]);
    }
}
