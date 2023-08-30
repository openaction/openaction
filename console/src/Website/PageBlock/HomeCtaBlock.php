<?php

namespace App\Website\PageBlock;

use App\Entity\Project;
use App\Form\Appearance\PageBlock\ConfigureCtaHomeBlockType;

class HomeCtaBlock extends AbstractBlock
{
    public const TYPE = 'cta';

    protected string $page = self::PAGE_HOME;
    protected string $type = self::TYPE;

    public function getConfigForm(): ?string
    {
        return ConfigureCtaHomeBlockType::class;
    }

    protected function getDefaultConfig(Project $project): array
    {
        return [
            'primary' => [
                'label' => 'Restons en contact',
                'target' => '/newsletter',
                'openNewTab' => false,
            ],
            'secondary' => [
                'label' => 'Nos actualités',
                'target' => '/posts',
                'openNewTab' => false,
            ],
        ];
    }
}
