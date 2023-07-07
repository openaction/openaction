<?php

namespace App\Form\Theme\Model;

use App\Entity\Organization;

class WebsiteThemeData
{
    public array $forOrganizations = [];

    /**
     * @param Organization[] $forOrganizations
     */
    public function __construct(iterable $forOrganizations)
    {
        foreach ($forOrganizations as $orga) {
            $this->forOrganizations[] = $orga->getId();
        }
    }
}
