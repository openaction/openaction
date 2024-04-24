<?php

namespace App\Form\Website\Model;

class PetitionData
{
    public ?\DateTime $startAt = null;

    public ?\DateTime $endAt = null;

    private ?int $signaturesGoal = null;
}
