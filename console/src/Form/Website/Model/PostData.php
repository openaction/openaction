<?php

namespace App\Form\Website\Model;

use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;

class PostData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public ?string $title = null;

    #[Assert\Length(max: 200, groups: ['Metadata'])]
    public ?string $quote = null;

    public ?string $content = '';

    #[Assert\Length(max: 200, groups: ['Metadata'])]
    public ?string $description = '';

    #[Assert\Length(max: 50, groups: ['Metadata'])]
    #[Assert\Regex(pattern: '/^[a-z]+:.+$/', groups: ['Metadata'])]
    public ?string $video = null;

    public ?string $publishedAt = null;

    public ?string $categories = null;

    public ?bool $onlyForMembers = false;

    #[Assert\Length(max: 250, groups: ['Metadata'])]
    #[Assert\Url(groups: ['Metadata'])]
    public ?string $externalUrl = null;

    public function __construct(?string $video)
    {
        $this->video = $video;
    }

    public function isPublication(): bool
    {
        return (bool) $this->publishedAt;
    }

    public function getCategoriesArray()
    {
        return Json::decode($this->categories) ?: [];
    }
}
