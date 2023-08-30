<?php

namespace App\DataExposer;

class DataExposer
{
    private array $data = [];

    public function expose(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    public function getExposedData(): array
    {
        return $this->data;
    }
}
