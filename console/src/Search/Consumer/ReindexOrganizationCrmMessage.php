<?php

namespace App\Search\Consumer;

final class ReindexOrganizationCrmMessage
{
    public function __construct(private int $organizationId)
    {
    }

    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }
}
