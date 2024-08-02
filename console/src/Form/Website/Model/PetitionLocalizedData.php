<?php

namespace App\Form\Website\Model;

use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;

class PetitionLocalizedData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public ?string $title = null;

    public ?string $content = '';

    #[Assert\Length(max: 200, groups: ['Metadata'])]
    public ?string $description = '';

    #[Assert\Length(max: 200, groups: ['Metadata'])]
    public ?string $addressedTo = '';

    public ?string $legalities = '';

    #[Assert\Length(max: 30, groups: ['Metadata'])]
    public ?string $submitButtonLabel = '';

    #[Assert\Length(max: 30, groups: ['Metadata'])]
    public ?string $optinLabel = '';

    #[Assert\Json(groups: ['Metadata'])]
    public ?string $categories = null;

    public function getCategoriesArray()
    {
        return Json::decode($this->categories) ?: [];
    }
}
