<?php

namespace App\Search\Consumer;

final class ExportCrmBatchMessage
{
    public function __construct(private int $jobId, private int $orgaId, private array $batchRequest)
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
        return $this->batchRequest;
    }
}
