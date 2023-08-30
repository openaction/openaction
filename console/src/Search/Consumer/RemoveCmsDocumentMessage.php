<?php

namespace App\Search\Consumer;

use App\Search\Model\Searchable;

final class RemoveCmsDocumentMessage
{
    public function __construct(
        private readonly string $documentId,
    ) {
    }

    public static function forSearchable(Searchable $entity): self
    {
        return new self($entity->getSearchType().'-'.$entity->getSearchUuid());
    }

    public function getDocumentId(): string
    {
        return $this->documentId;
    }
}
