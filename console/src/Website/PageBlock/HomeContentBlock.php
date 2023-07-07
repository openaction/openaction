<?php

namespace App\Website\PageBlock;

use App\Entity\Project;
use App\Form\Appearance\PageBlock\ConfigureContentHomeBlockType;

class HomeContentBlock extends AbstractBlock
{
    public const TYPE = 'content';

    protected string $page = self::PAGE_HOME;
    protected string $type = self::TYPE;

    public function getConfigForm(): ?string
    {
        return ConfigureContentHomeBlockType::class;
    }

    protected function getDefaultConfig(Project $project): array
    {
        return [
            'content' => '',
        ];
    }
}
