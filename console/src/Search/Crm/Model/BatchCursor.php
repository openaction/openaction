<?php

namespace App\Search\Crm\Model;

class BatchCursor
{
    private string $filenameFormat;
    private int $batchSize;

    private ?string $organizationUuid = null;
    private int $organizationCount = 0;
    private int $organizationBatchNumber = 1;
    private mixed $organizationStream = null;

    private array $allFiles = [];

    public function __construct(string $filenameFormat, int $batchSize)
    {
        $this->filenameFormat = $filenameFormat;
        $this->batchSize = $batchSize;
    }

    public function move(string $organizationUuid)
    {
        if ($organizationUuid !== $this->organizationUuid) {
            $this->organizationUuid = $organizationUuid;
            $this->organizationCount = 0;
            $this->organizationBatchNumber = 1;
            $this->organizationStream = $this->createStream();
        }

        ++$this->organizationCount;

        if ($this->organizationCount >= $this->batchSize) {
            $this->organizationCount = 0;
            ++$this->organizationBatchNumber;
            $this->organizationStream = $this->createStream();
        }
    }

    public function write(string $line)
    {
        fwrite($this->organizationStream, $line."\n");
    }

    public function getAllFiles(): array
    {
        return $this->allFiles;
    }

    private function createStream()
    {
        $filename = sprintf($this->filenameFormat, $this->organizationUuid, $this->organizationBatchNumber);
        $this->allFiles[$this->organizationUuid][] = $filename;

        return fopen($filename, 'wb');
    }
}
