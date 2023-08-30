<?php

namespace App\Search\Consumer;

use App\Search\Model\Searchable;

final class UpdateCmsDocumentMessage
{
    public function __construct(
        private readonly string $entityClass,
        private readonly int $entityId,
    ) {
    }

    public static function forSearchable(Searchable $entity): self
    {
        return new self($entity::class, $entity->getId());
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
