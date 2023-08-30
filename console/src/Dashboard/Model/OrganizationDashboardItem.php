<?php

namespace App\Dashboard\Model;

use App\Entity\Project;

class OrganizationDashboardItem
{
    private Project $project;
    private int $contacts;
    private int $members;

    public function __construct(Project $project, int $contacts, int $members)
    {
        $this->project = $project;
        $this->contacts = $contacts;
        $this->members = $members;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getContacts(): int
    {
        return $this->contacts;
    }

    public function getMembers(): int
    {
        return $this->members;
    }
}
