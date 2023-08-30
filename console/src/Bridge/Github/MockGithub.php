<?php

namespace App\Bridge\Github;

class MockGithub implements GithubInterface
{
    public array $files = [];

    public function getFileContent(string $installationId, string $repository, string $pathname): ?string
    {
        return $this->files[$installationId][$repository][$pathname] ?? null;
    }
}
