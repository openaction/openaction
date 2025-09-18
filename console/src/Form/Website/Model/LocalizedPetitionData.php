<?php

namespace App\Form\Website\Model;

use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;

class LocalizedPetitionData
{
    #[Assert\Length(max: 200)]
    public ?string $title = null;

    public ?string $content = '';

    public ?string $description = '';

    #[Assert\Length(max: 200, groups: ['Metadata'])]
    public ?string $submitButtonLabel = null;

    public ?string $optinLabel = null;

    public ?string $legalities = null;

    public ?string $addressedTo = null;

    public ?string $categories = null;

    public function getCategoriesArray()
    {
        return Json::decode($this->categories) ?: [];
    }
}
