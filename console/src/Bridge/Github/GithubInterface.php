<?php

namespace App\Bridge\Github;

interface GithubInterface
{
    public function getFileContent(string $installationId, string $repository, string $pathname): ?string;
}
