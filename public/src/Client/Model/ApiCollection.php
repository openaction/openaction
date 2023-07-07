<?php

namespace App\Client\Model;

class ApiCollection implements \IteratorAggregate, \Countable
{
    public array $data = [];
    public array $meta = [];

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }

    public function count(): int
    {
        return count($this->data);
    }
}
