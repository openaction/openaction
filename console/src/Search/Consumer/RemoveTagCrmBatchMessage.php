<?php

namespace App\Search\Consumer;

final class RemoveTagCrmBatchMessage
{
    public function __construct(private int $jobId, private int $orgaId, private array $request, private int $tagId)
    {
    }

    public function getJobId(): int
    {
        return $this->jobId;
    }

    public function getOrganizationId(): int
    {
        return $this->orgaId;
    }

    public function getBatchRequest(): array
    {
        return $this->request;
    }

    public function getTagId(): int
    {
        return $this->tagId;
    }
}
