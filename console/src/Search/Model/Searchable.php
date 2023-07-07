<?php

namespace App\Search\Model;

interface Searchable
{
    public function getSearchType(): string;

    public function isSearchPublic(): bool;

    public function getSearchOrganization(): string;

    public function getSearchAccessibleFromProjects(): array;

    public function getSearchId(): int;

    public function getSearchUuid(): string;

    public function getSearchTitle(): string;

    public function getSearchContent(): ?string;

    public function getSearchCategoriesFacet(): array;

    public function getSearchStatusFacet(): ?string;

    public function getSearchAreaTreeFacet(): array;

    public function getSearchDateFacet(): ?int;

    public function getSearchMetadata(): array;
}
