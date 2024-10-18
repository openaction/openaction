<?php

namespace App\Website\ImportExport\Parser;

use App\Entity\Community\ContentImport;

interface ExternalContentParserInterface
{
    public function getSupportedSource(): string;

    public function import(ContentImport $import, string $filename): void;
}
