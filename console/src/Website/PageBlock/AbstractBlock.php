<?php

namespace App\Website\PageBlock;

use App\Entity\Project;
use App\Entity\Website\PageBlock;
use App\Repository\Website\PageBlockRepository;

abstract class AbstractBlock implements BlockInterface
{
    private PageBlockRepository $repository;

    protected string $page;
    protected string $type;

    public function __construct(PageBlockRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function getDefaultConfig(Project $project): array
    {
        return [];
    }

    public function createBlock(Project $project): PageBlock
    {
        return new PageBlock(
            $project,
            $this->getPage(),
            $this->getType(),
            1 + $this->repository->count(['project' => $project]),
            $this->getDefaultConfig($project)
        );
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
