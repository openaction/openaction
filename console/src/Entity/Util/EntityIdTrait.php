<?php

namespace App\Entity\Util;

use Doctrine\ORM\Mapping as ORM;

trait EntityIdTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSearchId(): int
    {
        return $this->id;
    }
}
