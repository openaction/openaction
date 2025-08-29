<?php

namespace App\Form\Appearance\Model;

use App\Entity\Website\MenuItem;
use Symfony\Component\Validator\Constraints as Assert;

class WebsiteMenuItemData
{
    public ?MenuItem $parent = null;

    #[Assert\NotBlank]
    public ?string $label = null;

    #[Assert\NotBlank]
    public ?string $url = null;

    public ?bool $openNewTab = null;

    public static function createFromMenuItem(MenuItem $item): self
    {
        $self = new self();
        $self->parent = $item->getParent();
        $self->label = $item->getLabel();
        $self->url = $item->getUrl();
        $self->openNewTab = $item->isOpenNewTab();

        return $self;
    }
}
