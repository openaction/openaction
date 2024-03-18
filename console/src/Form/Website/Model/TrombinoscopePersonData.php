<?php

namespace App\Form\Website\Model;

use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;

class TrombinoscopePersonData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $fullName = null;

    #[Assert\Length(max: 250, groups: ['Metadata'])]
    public ?string $role = null;

    #[Assert\Length(max: 250, groups: ['Metadata'])]
    public ?string $description = null;

    public ?string $content = '';

    public ?string $publishedAt = null;

    #[Assert\Url]
    public ?string $socialWebsite = null;

    #[Assert\Email]
    public ?string $socialEmail = null;

    #[Assert\Url]
    public ?string $socialFacebook = null;

    #[Assert\Url]
    public ?string $socialTwitter = null;

    #[Assert\Url]
    public ?string $socialInstagram = null;

    #[Assert\Url]
    public ?string $socialLinkedIn = null;

    #[Assert\Url]
    public ?string $socialYoutube = null;

    #[Assert\Url]
    public ?string $socialMedium = null;

    public ?string $socialTelegram = null;

    public ?string $categories = null;

    public function isPublished()
    {
        return $this->publishedAt && new \DateTime($this->publishedAt) < new \DateTime();
    }

    public function getCategoriesArray()
    {
        return Json::decode($this->categories) ?: [];
    }
}
