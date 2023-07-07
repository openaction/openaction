<?php

namespace App\Search\Document;

use App\Search\Model\Searchable;
use App\Util\Uid;
use Symfony\Component\Uid\Uuid;

class SearchDocumentFactory
{
    public function createDocument(Searchable $searchable): array
    {
        return [
            'id' => $searchable->getSearchType().'-'.$searchable->getSearchUuid(),
            'uuid' => $searchable->getSearchUuid(),
            'encoded_uuid' => Uid::toBase62(new Uuid($searchable->getSearchUuid())),
            'title' => $searchable->getSearchTitle(),
            'type' => $searchable->getSearchType(),
            'status' => $searchable->getSearchStatusFacet(),
            'categories' => $searchable->getSearchCategoriesFacet(),
            'areas' => $searchable->getSearchAreaTreeFacet(),
            'date' => $searchable->getSearchDateFacet(),
            'metadata' => $searchable->getSearchMetadata(),
            'restrictions_is_public' => $searchable->isSearchPublic(),
            'restrictions_organization' => $searchable->getSearchOrganization(),
            'restrictions_projects' => $searchable->getSearchAccessibleFromProjects(),
            'content' => $searchable->getSearchContent(),
        ];
    }
}
