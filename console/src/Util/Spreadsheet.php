<?php

namespace App\Util;

use avadim\FastExcelReader\Excel;
use Symfony\Component\HttpFoundation\File\File;
use Traversable;

class Spreadsheet implements \IteratorAggregate, \Countable
{
    private function __construct(private readonly Excel $stream)
    {
        $this->stream->dateFormatter('Y-m-d H:i:s');
    }

    public static function open(File $file): ?self
    {
        return new self(Excel::open($file->getPathname()));
    }

    public function getFirstLines(int $limit): array
    {
        $lines = [];
        foreach ($this->stream->sheet()?->nextRow(false, Excel::KEYS_ZERO_BASED, rowLimit: $limit) as $row) {
            $lines[] = $row;
        }

        return $lines;
    }

    public function getIterator(): Traversable
    {
        $rows = [];
        foreach ($this->stream->readRows(false, Excel::KEYS_ZERO_BASED) as $row) {
            foreach ($row as $cell) {
                if ($cell) {
                    $rows[] = $row;
                    continue 2;
                }
            }
        }

        return new \ArrayIterator($rows);
    }

    public function count(): int
    {
        return $this->stream->sheet()?->countRows();
    }
}
