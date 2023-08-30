<?php

namespace App\Entity\Util;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait EntityUuidTrait
{
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $uuid = null;

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }
}
