<?php

namespace App\Dashboard;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Dashboard\Model\OrganizationDashboard;
use App\Dashboard\Model\OrganizationDashboardItem;
use App\Dashboard\Model\PartnerDashboard;
use App\Dashboard\Model\PartnerDashboardItem;
use App\Entity\Organization;
use App\Entity\User;
use App\Repository\OrganizationMemberRepository;
use App\Repository\OrganizationRepository;
use App\Repository\ProjectRepository;

class DashboardBuilder
{
    public function __construct(
        private OrganizationRepository $organizationRepository,
        private OrganizationMemberRepository $memberRepository,
        private ProjectRepository $projectRepository,
        private MeilisearchInterface $meilisearch,
    ) {
    }

    public function createOrganizationDashboard(Organization $organization, User $user): OrganizationDashboard
    {
        $index = $organization->getCrmIndexName();
        $contactsStats = $this->meilisearch->findFacetStats($index, 'projects');
        $membersStats = $this->meilisearch->findFacetStats($index, 'projects', ['filter' => ["status = 'm'"]]);

        // Create dashboard model
        $accessibleProjects = $organization->filterAccessibleProjects(
            $this->projectRepository->findByOrganization($organization),
            $this->memberRepository->findMember($user, $organization)
        );

        $globalProjects = [];
        $localProjects = [];
        $thematicProjects = [];

        foreach ($accessibleProjects as $project) {
            $item = new OrganizationDashboardItem(
                $project,
                $contactsStats[$project->getUuid()->toRfc4122()] ?? 0,
                $membersStats[$project->getUuid()->toRfc4122()] ?? 0
            );

            if ($project->isLocal()) {
                $localProjects[] = $item;
            } elseif ($project->isThematic()) {
                $thematicProjects[] = $item;
            } else {
                $globalProjects[] = $item;
            }
        }

        return new OrganizationDashboard($organization, $globalProjects, $localProjects, $thematicProjects);
    }

    public function createPartnerDashboard(User $partner): PartnerDashboard
    {
        // Find orgas
        $orgas = $this->organizationRepository->findByPartner($partner);

        // Find stats
        $projectsStats = $this->projectRepository->getPartnerDashboardStats($orgas);

        // Create dashboard model
        $items = [];
        foreach ($orgas as $orga) {
            $items[] = new PartnerDashboardItem($orga, $projectsStats[$orga->getId()] ?? 0);
        }

        return new PartnerDashboard($items);
    }
}
