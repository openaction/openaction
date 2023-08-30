<?php

namespace App\Website\PageBlock;

class HomeNewsletterBlock extends AbstractBlock
{
    public const TYPE = 'newsletter';

    protected string $page = self::PAGE_HOME;
    protected string $type = self::TYPE;

    public function getConfigForm(): ?string
    {
        return null;
    }
}
