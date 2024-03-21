<?php

namespace App\Form\Website\Model;

use App\Entity\Website\ManifestoTopic;
use Symfony\Component\Validator\Constraints as Assert;

class ManifestoTopicData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public ?string $title = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 6)]
    public ?string $color = '002851';

    public ?string $description = '';

    public function __construct(ManifestoTopic $topic)
    {
        $this->title = $topic->getTitle();
        $this->color = $topic->getColor();
        $this->description = $topic->getDescription();
    }
}
