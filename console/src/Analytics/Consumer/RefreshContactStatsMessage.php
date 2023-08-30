<?php

namespace App\Analytics\Consumer;

final class RefreshContactStatsMessage
{
    private int $organizationId;

    public function __construct(int $organizationId)
    {
        $this->organizationId = $organizationId;
    }

    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }
}
