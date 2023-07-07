<?php

namespace App\Website\PageBlock;

use App\Entity\Project;
use App\Form\Appearance\PageBlock\ConfigureSocialsHomeBlockType;

class HomeSocialsBlock extends AbstractBlock
{
    public const TYPE = 'socials';

    protected string $page = self::PAGE_HOME;
    protected string $type = self::TYPE;

    public function getConfigForm(): ?string
    {
        return ConfigureSocialsHomeBlockType::class;
    }

    protected function getDefaultConfig(Project $project): array
    {
        return [
            'facebook' => $project->getSocialFacebook(),
            'twitter' => $project->getSocialTwitter(),
        ];
    }
}
