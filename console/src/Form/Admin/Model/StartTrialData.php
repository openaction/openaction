<?php

namespace App\Form\Admin\Model;

use App\Platform\Plans;
use Symfony\Component\Validator\Constraints as Assert;

class StartTrialData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 60)]
    public ?string $name = '';

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [Plans::class, 'all'])]
    public ?string $plan;
}
