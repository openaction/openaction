<?php

namespace App\Entity\Util;

use App\Entity\Organization;
use Doctrine\ORM\Mapping as ORM;

trait EntityOrganizationTrait
{
    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Organization $organization = null;

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }
}
