<?php

namespace App\Community\ImportExport\Consumer;

final class ImportMessage
{
    public function __construct(private int $importId)
    {
    }

    public function getImportId(): int
    {
        return $this->importId;
    }
}
