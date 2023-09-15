<?php

namespace App\Website\PageBlock;

use App\Entity\Project;
use App\Form\Appearance\PageBlock\ConfigurePostsHomeBlockType;

class HomePostsBlock extends AbstractBlock
{
    public const TYPE = 'posts';

    protected string $page = self::PAGE_HOME;
    protected string $type = self::TYPE;

    public function getConfigForm(): ?string
    {
        return ConfigurePostsHomeBlockType::class;
    }

    protected function getDefaultConfig(Project $project): array
    {
        return [
            'category' => null,
            'label' => null,
        ];
    }
}
