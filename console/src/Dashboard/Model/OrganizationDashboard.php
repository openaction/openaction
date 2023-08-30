<?php

namespace App\Dashboard\Model;

use App\Entity\Organization;

class OrganizationDashboard
{
    private Organization $organization;
    private array $globalProjects;
    private array $localProjects;
    private array $thematicProjects;

    public function __construct(Organization $organization, array $globalProjects, array $localProjects, array $thematicProjects)
    {
        $this->organization = $organization;
        $this->globalProjects = $globalProjects;
        $this->localProjects = $localProjects;
        $this->thematicProjects = $thematicProjects;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    /**
     * @return OrganizationDashboardItem[]
     */
    public function getGlobalProjects(): array
    {
        return $this->globalProjects;
    }

    /**
     * @return OrganizationDashboardItem[]
     */
    public function getLocalProjects(): array
    {
        return $this->localProjects;
    }

    /**
     * @return OrganizationDashboardItem[]
     */
    public function getThematicProjects(): array
    {
        return $this->thematicProjects;
    }
}
