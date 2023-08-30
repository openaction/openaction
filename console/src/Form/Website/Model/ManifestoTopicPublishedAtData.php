<?php

namespace App\Form\Website\Model;

class ManifestoTopicPublishedAtData
{
    public ?string $publishedAt = null;

    public function isPublication(): bool
    {
        return (bool) $this->publishedAt;
    }
}
