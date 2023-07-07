<?php

namespace App\Search;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Entity\OrganizationMember;
use App\Platform\Permissions;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class TenantTokenManager
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private MeilisearchInterface $meilisearch,
        private EntityManagerInterface $em,
    ) {
    }

    public function refreshMemberCrmTenantToken(OrganizationMember $member, bool $persist = null)
    {
        $searchFilter = [];
        $permissions = $member->getProjectsPermissions();
        foreach ($this->projectRepository->findUuidsByOrganization($member->getOrganization()) as $projectUuid) {
            if ($permissions->hasPermission((string) $projectUuid, Permissions::COMMUNITY_CONTACTS_VIEW)) {
                $searchFilter[] = 'projects = '.$projectUuid;
            }
        }

        $member->setCrmTenantToken($this->meilisearch->createTenantToken(
            $member->getOrganization()->getCrmSearchKeyUid(),
            $member->getOrganization()->getCrmSearchKey(),
            [$member->getOrganization()->getCrmIndexName() => ['filter' => [$searchFilter]]]
        ));

        if ($persist) {
            $this->em->persist($member);
            $this->em->flush();
        }
    }
}
