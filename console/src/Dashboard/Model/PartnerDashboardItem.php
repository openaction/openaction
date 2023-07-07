<?php

namespace App\Dashboard\Model;

use App\Entity\Organization;

class PartnerDashboardItem
{
    private Organization $organization;
    private int $projectsCount;
    private int $contactsCount;

    public function __construct(Organization $organization, int $projectsCount, int $contactsCount)
    {
        $this->organization = $organization;
        $this->projectsCount = $projectsCount;
        $this->contactsCount = $contactsCount;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getProjectsCount(): int
    {
        return $this->projectsCount;
    }

    public function getContactsCount(): int
    {
        return $this->contactsCount;
    }
}
