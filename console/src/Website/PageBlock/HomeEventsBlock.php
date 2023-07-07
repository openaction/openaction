<?php

namespace App\Website\PageBlock;

use App\Entity\Project;
use App\Form\Appearance\PageBlock\ConfigureEventsHomeBlockType;

class HomeEventsBlock extends AbstractBlock
{
    public const TYPE = 'events';

    protected string $page = self::PAGE_HOME;
    protected string $type = self::TYPE;

    public function getConfigForm(): ?string
    {
        return ConfigureEventsHomeBlockType::class;
    }

    protected function getDefaultConfig(Project $project): array
    {
        return [
            'category' => null,
        ];
    }
}
