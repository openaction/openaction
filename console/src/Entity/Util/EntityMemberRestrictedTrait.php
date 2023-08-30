<?php

namespace App\Entity\Util;

use Doctrine\ORM\Mapping as ORM;

trait EntityMemberRestrictedTrait
{
    #[ORM\Column(type: 'boolean')]
    private bool $onlyForMembers = false;

    public function isOnlyForMembers(): bool
    {
        return $this->onlyForMembers;
    }
}
