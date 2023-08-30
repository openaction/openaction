<?php

namespace App\Search\Model;

class SearchableReference
{
    private string $entityClass;
    private int $entityId;

    public function __construct(string $entityClass, int $entityId)
    {
        $this->entityClass = $entityClass;
        $this->entityId = $entityId;
    }

    public static function forSearchable(Searchable $searchable): self
    {
        return new self($searchable::class, $searchable->getSearchId());
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }
}
