<?php

namespace App\Website\PageBlock;

use App\Entity\Project;
use App\Entity\Website\PageBlock;

interface BlockInterface
{
    public const PAGE_HOME = 'home';

    public function getPage(): string;

    public function getType(): string;

    public function createBlock(Project $project): PageBlock;

    public function getConfigForm(): ?string;
}
