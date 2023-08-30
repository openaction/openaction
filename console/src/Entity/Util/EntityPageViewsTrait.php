<?php

namespace App\Entity\Util;

use Doctrine\ORM\Mapping as ORM;

trait EntityPageViewsTrait
{
    #[ORM\Column(type: 'bigint')]
    private int $pageViews = 0;

    public function getPageViews(): int
    {
        return $this->pageViews;
    }
}
