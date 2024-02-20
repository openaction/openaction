<?php

namespace App\Community\ImportExport\Consumer;

final class ContentImportMessage
{
    public function __construct(private readonly int $importId)
    {
    }

    public function getImportId(): int
    {
        return $this->importId;
    }
}