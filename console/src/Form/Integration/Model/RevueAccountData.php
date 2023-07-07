<?php

namespace App\Form\Integration\Model;

use App\Entity\Integration\RevueAccount;
use Symfony\Component\Validator\Constraints as Assert;

class RevueAccountData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $label;

    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    public ?string $apiToken;

    public static function createFromAccount(RevueAccount $account): self
    {
        $self = new self();
        $self->label = $account->getLabel();
        $self->apiToken = $account->getApiToken();

        return $self;
    }
}
