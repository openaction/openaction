<?php

// PHPStorm inspection disabled as we want to check for type through the validator
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Api\Model;

use App\Entity\Community\PhoningCampaignCall;
use Symfony\Component\Validator\Constraints as Assert;

class PhoningCampaignCallApiData
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Choice(callback: [PhoningCampaignCall::class, 'getAvailableStatuses'])]
    public $status = '';

    #[Assert\All([new Assert\Type('string')])]
    #[Assert\Type('array')]
    public $answers = [];

    public static function createFromPayload(array $data): self
    {
        $self = new self();

        foreach (get_object_vars($self) as $var => $value) {
            $self->{$var} = $data[$var] ?? null;
        }

        return $self;
    }
}
