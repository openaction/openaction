<?php

namespace App\Client\Model;

class ApiResource extends \stdClass
{
    public string $type = 'ApiResource';
    public array $links = [];

    public function toArray(): array
    {
        return $this->castAsArrayRecursive(get_object_vars($this));
    }

    private function castAsArrayRecursive($resource)
    {
        if ($resource instanceof self) {
            return $resource->toArray();
        }

        if (is_iterable($resource)) {
            $collection = [];
            foreach ($resource as $key => $value) {
                $collection[$key] = $this->castAsArrayRecursive($value);
            }

            return $collection;
        }

        return $resource;
    }
}
