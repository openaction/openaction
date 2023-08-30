<?php

namespace App\Search\Consumer;

final class RemoveCrmDocumentMessage
{
    public function __construct(private int $organizationId, private string $contactUuid)
    {
    }

    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }

    public function getContactUuid(): string
    {
        return $this->contactUuid;
    }
}
