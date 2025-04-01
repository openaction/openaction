<?php

namespace App\Dashboard\Model;

use App\Entity\Organization;

class PartnerDashboardItem
{
    private Organization $organization;
    private int $projectsCount;

    public function __construct(Organization $organization, int $projectsCount)
    {
        $this->organization = $organization;
        $this->projectsCount = $projectsCount;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getProjectsCount(): int
    {
        return $this->projectsCount;
    }
}
