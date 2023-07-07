<?php

namespace App\Util;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\Common\Creator\ReaderEntityFactory;
use OpenSpout\Reader\ReaderInterface;
use OpenSpout\Reader\SheetInterface;
use Symfony\Component\HttpFoundation\File\File;

class Spreadsheet implements \Iterator
{
    private \Iterator $rows;

    private function __construct(\Iterator $rows)
    {
        $this->rows = $rows;
    }

    public static function open(File $file): ?self
    {
        if (!$sheet = self::openFirstSheet(self::createReader($file))) {
            return null;
        }

        return new self($sheet->getRowIterator());
    }

    public function getFirstLines(int $limit): array
    {
        $lines = [];
        foreach ($this as $row) {
            $lines[] = $row;

            if (count($lines) === $limit) {
                return $lines;
            }
        }

        return $lines;
    }

    public function current(): ?array
    {
        if (!$row = $this->rows->current()) {
            return null;
        }

        return self::normalizeRow($row);
    }

    public function next(): void
    {
        $this->rows->next();
    }

    public function key(): int
    {
        return $this->rows->key() - 1;
    }

    public function valid(): bool
    {
        return $this->rows->valid();
    }

    public function rewind(): void
    {
        $this->rows->rewind();
    }

    private static function createReader(File $file): ReaderInterface
    {
        $reader = match ($file->getMimeType()) {
            'application/vnd.oasis.opendocument.spreadsheet' => ReaderEntityFactory::createODSReader(),
            default => ReaderEntityFactory::createXLSXReader(),
        };

        $reader->open($file->getPathname());

        return $reader;
    }

    private static function openFirstSheet(ReaderInterface $reader): ?SheetInterface
    {
        $iterator = $reader->getSheetIterator();
        $iterator->rewind();

        return $iterator->valid() ? $iterator->current() : null;
    }

    private static function normalizeRow(Row $row): array
    {
        $data = [];

        $contentReached = false;
        foreach (array_reverse($row->toArray()) as $cell) {
            if (is_object($cell) || trim((string) $cell)) {
                $contentReached = true;
            }

            if ($contentReached) {
                $data[] = $cell instanceof \DateTime ? $cell->format('Y-m-d H:i:s') : trim($cell);
            }
        }

        return array_reverse($data);
    }
}
