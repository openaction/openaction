<?php

namespace App\Entity\Util;

use Doctrine\ORM\Mapping as ORM;

trait EntityExternalUrlTrait
{
    #[ORM\Column(length: 250, nullable: true)]
    private ?string $externalUrl = null;

    public function getExternalUrl(): ?string
    {
        return $this->externalUrl;
    }

    public function setExternalUrl(?string $externalUrl): void
    {
        $this->externalUrl = $externalUrl;
    }
}
