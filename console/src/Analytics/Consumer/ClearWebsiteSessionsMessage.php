<?php

namespace App\Analytics\Consumer;

final class ClearWebsiteSessionsMessage
{
    private int $projectId;

    public function __construct(int $projectId)
    {
        $this->projectId = $projectId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }
}
