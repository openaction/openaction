<?php

namespace App\Form\Website\Model;

use App\Entity\Website\ManifestoProposal;
use Symfony\Component\Validator\Constraints as Assert;

class ManifestoProposalData
{
    public const STATUSES = [
        ManifestoProposal::STATUS_TODO,
        ManifestoProposal::STATUS_IN_PROGRESS,
        ManifestoProposal::STATUS_DONE,
    ];

    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public ?string $title = null;

    public ?string $content = '';

    #[Assert\Choice(choices: ManifestoProposalData::STATUSES)]
    public ?string $status = null;

    #[Assert\Length(max: 250)]
    public ?string $statusDescription = null;

    #[Assert\Length(max: 50)]
    public ?string $statusCtaText = null;

    #[Assert\Length(max: 250)]
    public ?string $statusCtaUrl = null;
}
