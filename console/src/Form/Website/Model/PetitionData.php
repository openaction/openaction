<?php

namespace App\Form\Website\Model;

use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;

class PetitionData
{
    #[Assert\Length(max: 200)]
    public ?string $slug = null;

    public ?string $startAt = null;
    public ?string $endAt = null;

    public ?int $signaturesGoal = null;

    public ?string $authors = null;

    public function getAuthorsArray()
    {
        return Json::decode($this->authors) ?: [];
    }
}
