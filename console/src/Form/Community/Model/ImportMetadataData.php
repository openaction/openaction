<?php

namespace App\Form\Community\Model;

use App\Entity\Community\Import;

class ImportMetadataData
{
    public array $columnsTypes = [];
    public ?int $areaId = null;

    public static function createFromImport(Import $import): self
    {
        $self = new self();
        $self->areaId = $import->getArea() ? $import->getArea()->getId() : null;

        // Columns
        foreach ($import->getHead()->getMatchedColumns() as $columnType) {
            $self->columnsTypes[] = $columnType ?: 'ignored';
        }

        return $self;
    }
}
