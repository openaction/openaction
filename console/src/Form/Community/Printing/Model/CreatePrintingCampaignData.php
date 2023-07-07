<?php

namespace App\Form\Community\Printing\Model;

use App\Platform\Products;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePrintingCampaignData
{
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [Products::class, 'getPrintProducts'])]
    public ?string $product = '';
}
