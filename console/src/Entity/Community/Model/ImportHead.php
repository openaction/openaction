<?php

namespace App\Entity\Community\Model;

class ImportHead
{
    private array $columns;
    private array $firstLines;
    private array $matchedColumns;

    public function __construct(array $columns, array $firstLines, array $matchedColumns)
    {
        $this->columns = $columns;
        $this->firstLines = $firstLines;
        $this->matchedColumns = $matchedColumns;
    }

    public static function createFromData(array $data): self
    {
        return new self($data['columns'], $data['firstLines'], $data['matchedColumns']);
    }

    public function toArray(): array
    {
        return [
            'columns' => $this->columns,
            'firstLines' => $this->firstLines,
            'matchedColumns' => $this->matchedColumns,
        ];
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getFirstLines(): array
    {
        return $this->firstLines;
    }

    public function getMatchedColumns(): array
    {
        return $this->matchedColumns;
    }

    public function setMatchedColumns(array $matchedColumns)
    {
        $this->matchedColumns = $matchedColumns;
    }
}
