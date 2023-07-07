<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\Document;
use App\Util\Uid;

class DocumentTransformer extends AbstractTransformer
{
    private CdnRouter $cdnRouter;

    public function __construct(CdnRouter $cdnRouter)
    {
        $this->cdnRouter = $cdnRouter;
    }

    public function transform(Document $document)
    {
        return [
            '_resource' => 'Document',
            '_links' => [
                'self' => $this->createLink('api_website_documents_view', ['id' => Uid::toBase62($document->getUuid())]),
            ],
            'id' => Uid::toBase62($document->getUuid()),
            'name' => $document->getName(),
            'file' => $this->cdnRouter->generateUrl($document->getFile()),
            'created_at' => $document->getCreatedAt()->format(\DateTime::ATOM),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'Document';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            '_links' => [
                'self' => 'string',
            ],
            'id' => 'string',
            'name' => 'string',
            'file' => 'string',
            'created_at' => 'string',
        ];
    }
}
