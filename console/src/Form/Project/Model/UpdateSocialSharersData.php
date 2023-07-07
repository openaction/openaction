<?php

namespace App\Form\Project\Model;

use App\Entity\Model\SocialSharers;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateSocialSharersData
{
    #[Assert\All([new Assert\Choice(['callback' => [SocialSharers::class, 'getAllSocialSharers']])])]
    public ?array $sharers = [];

    public function __construct(SocialSharers $sharers)
    {
        $this->sharers = $sharers->toArray();
    }
}
