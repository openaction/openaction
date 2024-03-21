<?php

namespace App\Form\Website\Model;

use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;

class PageData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public ?string $title = null;

    public ?string $content = '';

    public ?string $description = '';

    #[Assert\Json(groups: ['Metadata'])]
    public ?string $categories = null;

    public ?string $parentId = null;

    public ?bool $onlyForMembers = false;

    public function getCategoriesArray()
    {
        return Json::decode($this->categories) ?: [];
    }
}
