<?php

namespace App\Form\Partner\Model;

use App\Entity\Model\PartnerMenu;
use Symfony\Component\Validator\Constraints as Assert;

class PartnerMenuData
{
    #[Assert\Length(max: 25)]
    public ?string $label1 = null;

    #[Assert\Url]
    public ?string $url1 = null;

    #[Assert\Length(max: 25)]
    public ?string $label2 = null;

    #[Assert\Url]
    public ?string $url2 = null;

    public static function createFromMenu(PartnerMenu $menu): self
    {
        $self = new self();
        foreach ($menu->getItems() as $key => $item) {
            $self->{'label'.($key + 1)} = $item->getLabel();
            $self->{'url'.($key + 1)} = $item->getUrl();
        }

        return $self;
    }

    public function toArray(): array
    {
        $items = [];
        for ($i = 1; $i <= 2; ++$i) {
            if ($this->{'label'.$i} && $this->{'url'.$i}) {
                $items[] = [
                    'label' => $this->{'label'.$i},
                    'url' => $this->{'url'.$i},
                ];
            }
        }

        return $items;
    }
}
