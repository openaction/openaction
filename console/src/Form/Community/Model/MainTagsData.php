<?php

namespace App\Form\Community\Model;

use App\Entity\Organization;

class MainTagsData
{
    public iterable $tags = [];
    public bool $isProgress;

    public static function createFromOrganization(Organization $organization): self
    {
        $self = new self();
        $self->isProgress = $organization->mainTagsIsProgress();

        for ($i = 0; $i <= 7; ++$i) {
            $self->tags[] = null;
        }

        foreach ($organization->getMainTags() as $key => $mainTag) {
            $self->tags[$key] = $mainTag->getTag();
        }

        return $self;
    }
}
