<?php

namespace App\Community\ImportExport\Consumer;

final class ExportMessage
{
    private string $locale;
    private string $email;
    private int $organizationId;
    private ?int $tagId = null;

    public function __construct(string $locale, string $email, int $organizationId, ?int $tagId)
    {
        $this->locale = $locale;
        $this->email = $email;
        $this->organizationId = $organizationId;
        $this->tagId = $tagId;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }

    public function getTagId(): ?int
    {
        return $this->tagId;
    }
}
