<?php

namespace App\Website;

use App\Entity\Project;
use App\Entity\Website\PageBlock;
use App\Website\PageBlock\BlockInterface;

class PageBlockManager
{
    /**
     * @var BlockInterface[][]
     */
    private array $blocks;

    /**
     * @var BlockInterface[]|iterable
     */
    public function __construct(iterable $blocks)
    {
        $this->blocks = [];
        foreach ($blocks as $block) {
            $this->blocks[$block->getPage()][$block->getType()] = $block;
        }
    }

    public function getTypes(string $page): array
    {
        return array_keys($this->blocks[$page] ?? []);
    }

    public function createBlock(Project $project, string $page, string $type): PageBlock
    {
        return $this->blocks[$page][$type]->createBlock($project);
    }

    public function getConfigForm(PageBlock $block): ?string
    {
        return $this->blocks[$block->getPage()][$block->getType()]->getConfigForm();
    }
}
