<?php

namespace App\Form\Organization\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CreateBatchProjectData
{
    /**
     * @var CreateBatchProjectItemData[]
     */
    #[Assert\NotBlank]
    #[Assert\Valid]
    public array $items = [];
}
