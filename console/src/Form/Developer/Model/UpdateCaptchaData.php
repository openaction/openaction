<?php

namespace App\Form\Developer\Model;

use App\Entity\Project;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateCaptchaData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public $siteKey;

    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public $secretKey;

    public static function createFromProject(Project $item): self
    {
        $self = new self();
        $self->siteKey = $item->getWebsiteTurnstileSiteKey();
        $self->secretKey = $item->getWebsiteTurnstileSecretKey();

        return $self;
    }
}
