<?php

namespace App\Util;

use avadim\FastExcelReader\Excel;
use avadim\FastExcelReader\Sheet;
use Symfony\Component\HttpFoundation\File\File;

class Spreadsheet implements \IteratorAggregate, \Countable
{
    private function __construct(private readonly Sheet $sheet)
    {
    }

    public static function open(File $file): ?self
    {
        $stream = Excel::open($file->getPathname());
        $stream->dateFormatter('Y-m-d H:i:s');

        // Workaround to fix the default dimension not being present on Sheet
        $sheet = $stream->sheet();
        $reflection = new \ReflectionObject($sheet);
        $property = $reflection->getProperty('dimension');
        $property->setAccessible(true);
        $property->setValue($sheet, ['range' => '']);

        return new self($sheet);
    }

    public function getFirstLines(int $limit): array
    {
        $rows = [];
        foreach ($this->sheet->nextRow(false, Excel::KEYS_ZERO_BASED, rowLimit: $limit) as $row) {
            foreach ($row as $cell) {
                if ($cell) {
                    $rows[] = $row;
                    continue 2;
                }
            }
        }

        return $rows;
    }

    public function getIterator(): \Traversable
    {
        $rows = [];
        foreach ($this->sheet->readRows(false, Excel::KEYS_ZERO_BASED) as $row) {
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
        return iterator_count($this->getIterator());
    }
}
